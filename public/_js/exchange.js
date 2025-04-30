/**
 * 
 * @param {number} user ID de l'utilisateur
 * @param {string} exchange UUID de l'echange
 * @param {string} message 
 */
async function sendMessage(user, exchange, message, callBack) {
    console.dir({ user, exchange, message });
    const requestOptions = {
        method: "GET",
        redirect: "follow"
    };

    fetch("http://127.0.0.1/MutuForumV2/ajaxTest.php?act=addExchangeMessage&uuidExchange=" + exchange + "&ID_USER=" + user + "&message=" + message, requestOptions)
        .then((response) => response.text())
        .then((result) => {
            let r = JSON.parse(result);
            if (r.error != undefined) {
                _alert("Une erreur s'est produite");
            } else {
                callBack.call(this, r);
            }
        })
        .catch((error) => console.error(error));
}
/**
 * 
 * @param {string} exchange uuid de l'échange 
 * @param {*} callBack 
 */
async function getMessage(exchange, callBack, timer) {
    const requestOptions = {
        method: "GET",
        redirect: "follow"
    };
    let interval = setInterval(
        function () {
            fetch("http://127.0.0.1/MutuForumV2/ajaxTest.php?act=getExchangeMessage&uuidExchange=" + exchange, requestOptions)
                .then((response) => response.text())
                .then((result) => {
                    let r = JSON.parse(result);
                    if (r.error != undefined) {
                        switch (r.error) {
                            case 0:
                                _alert("Une erreur s'est produite");
                                break;
                            case 1: {
                                _alert("il n'y a pas d'échanges")
                                break;
                            }
                            case 1: {
                                _alert("il n'y a pas d'échanges")
                                break;
                            }
                        }

                        clearInterval(interval);
                    } else {
                        callBack.call(this, r.data);
                    }
                })
                .catch((error) => {
                    console.error(error);
                    clearInterval(interval);
                });
        }, timer
    );
}