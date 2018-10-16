import AppConfig from "../config";

export default class UrlHelper {

    static imageUrl(imgPath) {
        if (imgPath.indexOf("http") === 0) return imgPath;
        if (imgPath.indexOf("//") === 0) return imgPath;

        return AppConfig.STORAGE_URL + imgPath;
    }

    // absolute URL to public folder
    static assetUrl(path) {
        return AppConfig.ROOT_URL + path;
    }

};
