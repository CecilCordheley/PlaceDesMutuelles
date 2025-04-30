function _alert(msg, callBack = undefined,cat=0) {
    //Overlay de l'alerte
    let classes=["normal","warning","information"]
    let container = document.createElement("div");
    container.classList.add("alert_overlay");
    container.onclick = function () {
        this.remove();
        if (callBack != undefined) {
            callBack.call();
        }
    }
    //Div du Message
    let message = document.createElement("div");
    message.classList.add(classes[cat]);
    message.innerHTML = "<p>" + msg + "</p>";
    container.appendChild(message);
    document.body.appendChild(container);
}
function setTitleAttr(el) {
    let m = el.title;
    let div = document.createElement("div");
    div.classList.add("titleAttr");
    div.innerHTML = m;
    div["style"]["top"] = el.offsetTop;
    el.onmouseover = function () {
        document.body.appendChild(div);
    }
    el.onmouseout = function () {
        div.remove();
    }
}