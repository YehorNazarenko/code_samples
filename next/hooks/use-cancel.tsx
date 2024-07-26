import { useCallback } from 'react';
import { useRouter } from 'next/router';

export default function useCancel(entity: string) {
    const router = useRouter();

    const onCancel = useCallback(
        () => router.push(`/${entity}`),
        [router, entity],
    );
    return { onCancel };
}
