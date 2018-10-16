export default class Array {

    static inArray(element,array) {
        for(var i=0;i<array.length;i++)
        {
            if(array[i]===element){return true;}
        }
        return false;
    }

    static twoArrayHasSameElement(array1, array2){
        for(var i=0;i<array1.length;i++)
        {
            if(array2.indexOf(array1[i])!= -1){
                return true;
            }
        }
        return false;
    }

};
