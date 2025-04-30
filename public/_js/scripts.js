/*!
* Start Bootstrap - Shop Homepage v5.0.6 (https://startbootstrap.com/template/shop-homepage)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-homepage/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project
window.addEventListener("load", function () {
    document.querySelectorAll("select[selected_value]").forEach(el => {
        el.value = el.getAttribute("selected_value");
    })
})
async function banUser(id) {
    data = {
        comment: document.getElementById("BanComment").value
    }
    let response = await fetch('http://127.0.0.1/MutuForumV2/ajaxTest.php?act=banUser&id=' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(data)
    });
    let result = await response.json();
    if (result.error != undefined) {
        alert(result.message);
        return false;
    } else {
        return result.result == "ok";
    }
}
async function getUserInfo(id) {
    const myHeaders = new Headers();
    myHeaders.append("Cookie", "PHPSESSID=ka7tq3g6tqqalk9giisr86g2e1");
    myHeaders.append('Access-Control-Allow-Origin', '*');
    const requestOptions = {
        method: "GET",
        headers: myHeaders
    };

    fetch("http://127.0.0.1/MutuForumV2/ajaxTest.php?act=getUser&id=" + id, requestOptions)
        .then((response) => response.text())
        .then((result) => {
            let r = (JSON.parse(result));
            if (r.result != undefined) {
                Object.entries(r.data).forEach(el => {
                    let htmlElement = document.querySelector(`[data-async=${el[0]}]`);
                    console.log(htmlElement ?? "-");
                    if (htmlElement != undefined)
                        htmlElement.innerHTML = el[1];
                });
            } else {
                console.error(r.message);
            }
        });
}
/**
 * Filtres les messages selon l'organisme au quel appartient l'utilisateur de celui-ci
 * @param {int} id_orga 
 */
function filterMessage(id_orga) {
    if (id_orga != null) {
        let container = document.querySelectorAll(`#messages tbody tr:not([ORGA_USER='${id_orga}'])`);
        container.forEach(el => {
            el.style.display = "none";
        })
    } else {
        let container = document.querySelectorAll(`#messages tbody tr`);
        container.forEach(el => {
            el.style.display = "table-row";
        })
    }
}
function getMessages(id) {
    const myHeaders = new Headers();
    myHeaders.append("Cookie", "PHPSESSID=ka7tq3g6tqqalk9giisr86g2e1");
    myHeaders.append('Access-Control-Allow-Origin', '*');
    const requestOptions = {
        method: "GET",
        headers: myHeaders
    };

    fetch("http://127.0.0.1/MutuForumV2/ajax.php?act=getMessage&id=" + id, requestOptions)
        .then((response) => response.text())
        .then((result) => {
            let r = (JSON.parse(result));
            let container = document.querySelector("#messages tbody");
            if (r.result == "OK") {

                container.innerHTML = "";
                r.data.forEach((el) => {
                    let l = document.createElement("tr");
                    l.setAttribute("ORGA_USER", el.USER.ID_ORGA)
                    l.innerHTML += `<td>${el.ID_MESSAGE}</td><td>${el.DATE_MESSAGE}</td><td>${el.CONTENT_MESSAGE}</td><td><a href='UserGestion-see_${el.USER.ID_USER}'>${el.USER.PSEUDO_USER}</a></td>`
                    let button = document.createElement("button");
                    button.classList.add("btn");

                    if (el.DISPLAY_ == 1) {
                        button.innerHTML = "<i class=\"fa-solid fa-eye-slash\"></i>";
                        button.classList.add("btn-warning");
                        button.title = "Masquer le message";
                    } else {
                        button.innerHTML = "<i class=\"fa-solid fa-eye\"></i>";
                        button.classList.add("btn-success");
                        button.title = "Afficher le message";
                    }
                    button.onclick = function () {
                        ToogleDisplay(el.ID_MESSAGE,(result)=>{
                            if (result.displayValue == "1") {
                                this.classList.remove("btn-success");
    
                                this.classList.add("btn-warning");
                                this.title = "Masquer le message";
                                this.innerHTML = "<i class=\"fa-solid fa-eye-slash\"></i>";
                                alert("Le message est de nouveau accèssible sur le forum")
                            } else {
                                this.classList.remove("btn-warning");
                                this.classList.add("btn-success");
                                this.innerHTML = "<i class=\"fa-solid fa-eye\"></i>";
                                this.title = "Afficher le message";
                                alert("Le message n'est plus accèssible sur le forum")
                            }
                        });
                    }

                    let containerBtn = document.createElement("td");
                    containerBtn.appendChild(button);
                    l.appendChild(containerBtn);
                    container.appendChild(l);
                });
            } else {
                container.innerHTML = "<tr><td colspan=4'>Il n'y a pas de message</td></tr>"
            }
        })
        .catch((error) => console.error(error));
}
async function ToogleDisplay(idMessage,callback) {
    const myHeaders = new Headers();
    myHeaders.append("Cookie", "PHPSESSID=ka7tq3g6tqqalk9giisr86g2e1");
    myHeaders.append('Access-Control-Allow-Origin', '*');
    const requestOptions = {
        method: "GET",
        headers: myHeaders
    };

    fetch("http://127.0.0.1/MutuForumV2/ajaxTest.php?act=toolgeDisplayMessage&id=" + idMessage, requestOptions)
        .then((response) => response.text())
        .then((result) => {
            let r = (JSON.parse(result));
            if (r.result != undefined) {
                callback.call(this,r);
            } else {
                console.error(r.message);
            }
        });
}
setUserButtons = function () {
    console.info("userButton reset");
    $('[name=addData]').click(function () {
        addDataLine();
        this.remove();
        return false;
    });
    $('[name=removeData]').click(function (e) {
        e.preventDefault();
        $(this).parent().parent().remove()
        let t = document.querySelector('#USERDATA>tbody');
        if (t.children.length == 0) {
            let line = document.createElement('tr');
            line.innerHTML = "<td colspan=3><a href='#' name='addData' class='btn btn-success'><i class='fa-solid fa-plus'></i></a></td>"

            t.appendChild(line);
            setUserButtons();
        }
        return false;
    });
}
function reservContact(date, time) {

}
function addDataLine() {
    let t = document.querySelector("#USERDATA>tbody");
    let l = document.createElement("tr");
    l.innerHTML = "<td><input name=\"DATA_KEY[]\" class=\"form-control\" type=\"text\" placeholder='Clé de la donnée'></td>" +
        "<td><input name=\"DATA_VALUE[]\" class=\"form-control\" type=\"text\" placeholder='Valeur'></td>" +
        "<td>" +
        "<button name=\"addData\" class=\"btn btn-success\">" +
        "<i class=\"fa-solid fa-plus\"></i>" +
        "</button>" +
        "</td>" +
        "<td>" +
        "<button name=\"removeData\" class=\"btn btn-danger\">" +
        "<i class=\"fa-solid fa-minus\"></i>" +
        "</button>" +
        "</td>"
    t.appendChild(l);
    setUserButtons();
}
function checkMessage() {
    //Récupérer le message
    let message = document.querySelector('#messageInput');
    if (message.value.length == 0) {
        alert("Votre message est vide !");
        message.classList.add("unvalide");
        return false;
    } else {
        const regex = /<([a-z A-Z]+)>/u;
        if ((m = regex.exec(message.value)) !== null) {
            alert("Vous ne pouvez pas placer de balises html dans votre message !!");
            message.classList.add("unvalide");
            return false;
        }
    }
    return true;
}