
document.addEventListener("DOMContentLoaded", function(event) {

    /* add class 'noSideNav' in #left ul li   */
    if(document.getElementById('ContaoDynaContent')) {
        if (document.getElementById('ContaoDynaContent').getElementsByTagName('li').length <= 1) {
            document.getElementById('ContaoDynaContent').classList.add("noSideNav");
        }
    }

});
