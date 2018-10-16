const appStorage = {
    setItem(key, value) {
        localStorage.setItem(key, value);
    },
    getItem(key) {
        return localStorage.getItem(key);
    },
    removeItem(key) {
        localStorage.removeItem(key);
    }
};

const userTokenKey = "user_token";
const userInfoKey = "user_info";
const userPermissionsKey = "user_permissions";

export const setToken = (token) => appStorage.setItem(userTokenKey, token);
export const getToken = () => appStorage.getItem(userTokenKey);
export const clearToken = () => appStorage.removeItem(userTokenKey);

export const setUserInfo = (user) => appStorage.setItem(userInfoKey, JSON.stringify(user));
export const getUserInfo = () => JSON.parse(appStorage.getItem(userInfoKey));
export const clearUserInfo = () => appStorage.removeItem(userInfoKey);

export const setUserPermissions = (permissions) => appStorage.setItem(userPermissionsKey, JSON.stringify(permissions));
export const getUserPermissions = () => JSON.parse(appStorage.getItem(userPermissionsKey)) || {};
export const clearUserPermissions = () => appStorage.removeItem(userPermissionsKey);
