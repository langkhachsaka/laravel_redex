import Converter from "./Converter";

export default class Validator {
    static required = value => value ? undefined : 'Thông tin bắt buộc';

    static requireInt = value =>
        value && !/^\-?\d+$/.test(value) ? "Nội dung phải là số nguyên" : undefined;

    static requireFloat = value =>
        value && !/^\-?\d+(\.\d+)?$/.test(value) ? "Nội dung phải là số" : undefined;

    static requireDiscountValue = value =>
        value && !/^\d+(\.\d+)?%?$/.test(value) ? "Giá trị chiết khấu không hợp lệ" : undefined;

    static maxLength = max => value =>
        value && value.length > max ? `Must be ${max} characters or less` : undefined;

    static number = value => value && isNaN(Number(value)) ? 'Dữ liệu phải là số' : undefined;

    static greaterThan = (min, errorMsg) => value =>
        value && value <= min ? errorMsg : undefined;
    static greaterThan0 = Validator.greaterThan(0, "Số phải lớn hơn 0");

    static greaterOrEqual = (min, errorMsg) => value =>
        value && value < min ? errorMsg : undefined;
    static greaterOrEqual0 = Validator.greaterOrEqual(0, "Số phải lớn hơn hoặc bằng 0");
    static greaterOrEqual1 = Validator.greaterOrEqual(1, "Số phải lớn hơn hoặc bằng 1");

    static lessOrEqual = (max, errorMsg) => value =>
        value && value > max ? errorMsg : undefined;
    static lessOrEqual12 = Validator.lessOrEqual(12, "Số phải nhỏ hơn hoặc bằng 12");
    static lessOrEqual31 = Validator.lessOrEqual(31, "Số phải nhỏ hơn hoặc bằng 31");
    static lessOrEqual30 = Validator.lessOrEqual(30, "Số phải nhỏ hơn hoặc bằng 30");
    static lessOrEqual29 = Validator.lessOrEqual(29, "Số phải nhỏ hơn hoặc bằng 29");
    static lessOrEqual28 = Validator.lessOrEqual(28, "Số phải nhỏ hơn hoặc bằng 28");
    static lessOrEqual100 = Validator.lessOrEqual(100, "Số phải nhỏ hơn hoặc bằng 100");

    static email = value =>
        value && !/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value) ?
            'Email không hợp lệ' : undefined;

    static username = value =>
        value && !/^[A-Z0-9._]+$/i.test(value) ?
            'Tên tài khoản không hợp lệ' : undefined;

    static phoneNumber = value =>
        value && !/^\+?[0-9]+[0-9\-\s]+$/i.test(value) ?
            'Số điện thoại không hợp lệ' : undefined;

    static noSpecialCharacter = value =>
        value && /[!@#\$%\^&\*\(\)\-_\+<>\?:"\[\]{}\|\\/\.,~`]+/i.test(value) ?
            'Nội dung không được chứa các ký tự đặc biệt' : undefined;
};
