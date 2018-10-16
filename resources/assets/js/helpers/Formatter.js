export default class Formatter {
    static money(n) {
        n = Formatter.number(n);
        return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    static number(n) {
        return Math.round(n * 100) / 100;
    }
};
