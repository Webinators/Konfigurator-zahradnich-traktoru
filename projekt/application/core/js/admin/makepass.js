function makepasswords(pass, corectalphabet, newalphabet) {

    newalphabet = newalphabet.split(",");
    corectalphabet = corectalphabet.split(",");

    var stringArray = pass.split('');
    var stringLength = stringArray.length - 1;

    function searchKey(Array, what, callback) {

        for (var i = 0; i < Array.length; i++) {

            if (what == Array[i]) {
                return i;
            }
        }

        return -1;
    }

    for (var i = 0; i <= stringLength; i++) {

        var newKey = searchKey(corectalphabet, stringArray[i]);

        if (newKey != -1) {
            stringArray[i] = newalphabet[newKey];
        }
    }

    var newString = stringArray.toString();
    newString = replaceAll(",", "", newString);

    return newString;
}