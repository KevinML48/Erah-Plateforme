export function isAdmin(user) {
    return user?.role === 'admin';
}

export function isAuthenticated(user) {
    return Boolean(user?.id);
}
