export default class Converter {
    static str2int(s) {
        if (typeof s !== 'string') return s;

        return parseInt(s, 0);
    }

    static str2float(s) {
        if (typeof s !== 'string') return s;

        return parseFloat(s);
    }

    static bool2int(bool) {
        return bool ? 1 : 0;
    }
};
