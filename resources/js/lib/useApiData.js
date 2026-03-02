import { useCallback, useEffect, useState } from 'react';

import api, { apiErrorMessage } from './api';

export default function useApiData(endpoint, { enabled = true, initialData = null } = {}) {
    const [data, setData] = useState(initialData);
    const [loading, setLoading] = useState(Boolean(enabled));
    const [error, setError] = useState(null);

    const reload = useCallback(async () => {
        if (!endpoint || !enabled) {
            setLoading(false);
            return null;
        }

        setLoading(true);
        setError(null);

        try {
            const response = await api.get(endpoint);
            const payload = response?.data?.data ?? response?.data ?? null;
            setData(payload);
            return payload;
        } catch (err) {
            setError(apiErrorMessage(err));
            return null;
        } finally {
            setLoading(false);
        }
    }, [enabled, endpoint]);

    useEffect(() => {
        reload();
    }, [reload]);

    return { data, loading, error, reload, setData };
}
