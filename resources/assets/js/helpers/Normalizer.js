import Formatter from "./Formatter";

export default class Normalizer {

    static money = (value) => {
        if (!value) {
            return value
        }

        const onlyNums = value.replace(/[^\d]/g, '');
        if (onlyNums.length <= 3) {
            return onlyNums
        }

        return Formatter.money(onlyNums);
    }

};
