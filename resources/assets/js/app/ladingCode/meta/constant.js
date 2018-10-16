const resourceName = 'lading-code';

const Constant = {

    resourcePath: function (p = null) {
        if (!p) return resourceName;

        return resourceName + '/' + p;
    },

    MORPH_TYPE_ORDER_VN: "Modules\\CustomerOrder\\Models\\CustomerOrder",
    MORPH_TYPE_BILL_OF_LADING: "Modules\\BillOfLading\\Models\\BillOfLading",

};

export default Constant;
