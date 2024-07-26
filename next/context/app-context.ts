import React from 'react';

export type AppApi = {
    openModel(): void;
    closeModal(): void;
};

export const AppContext = React.createContext<AppApi>({} as AppApi);
