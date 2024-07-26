import { useEffect, useState } from 'react';

import createCancellablePromise from '../../utils/create-cancellable-promise';

export type UseAsyncResult<Value> =
    | { isLoading: true; value: undefined; error: undefined }
    | { isLoading: false; value: Value; error: undefined }
    | { isLoading: false; value: undefined; error: Error };

export default function useAsync<Value>(
    asyncFn: (...props: unknown[]) => Promise<Value>,
    deps: unknown[],
): UseAsyncResult<Value> {
    const [value, setValue] = useState<Value>();
    const [error, setError] = useState<Error>();
    const [isLoading, setIsLoading] = useState<boolean>(true);

    useEffect(() => {
        const cancellablePromise = createCancellablePromise(asyncFn());

        setIsLoading(true);
        setError(undefined);
        setValue(undefined);

        cancellablePromise.promise
            .then((value) => {
                setIsLoading(false);
                setValue(value);
            })
            .catch((error) => {
                setIsLoading(false);
                setError(error);
            });

        return cancellablePromise.cancel;
    }, deps);

    if (isLoading) {
        return { isLoading: true, error: undefined, value: undefined };
    }

    if (error) {
        return { isLoading: false, error, value: undefined };
    }

    return { value: value as Value, isLoading: false, error: undefined };
}
