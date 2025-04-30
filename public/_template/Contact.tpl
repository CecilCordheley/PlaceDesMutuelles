<style>
    .agenda-jours {
        height: 500px;
    }

    .agenda-jours h4 {
        font-size: .9rem;
    }

    #slots-list {
        display: flex;
        flex-wrap: wrap;
    }

    #slots-list li {
        list-style: none;
        border: 1px solid #FFF;
        padding: 3px;
        margin: 1px;
    }

    #slots-list li.occuped {
        background: #922
    }

    .exchange .messages {
        height: 450px;
        border: 1px solid #999;
        width: 250px;
        display: flex;
        flex-direction: column;
    }

    .exchange .messages>div {
        width: 50%;
    }

    .exchange .messages .loc {
        float: right;
    }

    .exchange .messages .current {
        float: left;
    }
</style>
<h2>Contacter {var:user.ORGANISME}</h2>
<div class="row">
    <div class="agenda-jours col-2">
        <h4>Séléctionnez un jour</h4>
        <ul class="list-group">
            {LOOP:joursDisponibles}
            <li>
                <button class="btn btn-day" data-day="{#JOUR#}" data-date="{#DATE#}">{#JOUR#}</button>
            </li>
            {/LOOP}
        </ul>
    </div>
    <div class="col-4">

        <h4>Vos prochain échanges</h4>
        <ul>
            {LOOP:exchange}
            <li>{#dateExchange#} - {#HeureDebut#}</li>
            {/LOOP}
        </ul>

    </div>
    <div class="exchange col-4">
        {:IF {var:user.TYPE_UTILISATEUR}=1 & {var:exchange.HeureDebut}!0}
        <h4>Votre prochain echange : <span>{var:exchange.HeureDebut}</span></h4>
        <div class="messages">

        </div>
        <input type="text" id="internalMessage" data-exchangeID="{var:exchange.uuidExchange}">
        <button id="triggerMessage">Envoyer</button>
        {:ELSE:}
        <button class="btn btn-secondary" id="reloadMessage">Recharger</button>
        {:/IF}
    </div>
    <div class="agenda-slots col-4" style="display: none;">
        <h3>Créneaux disponibles pour <span id="selected-day"></span></h3>
        <ul id="slots-list">
            <!-- Dynamically filled via JavaScript or Backend -->
        </ul>
    </div>
</div>


</div>
<script>
    function exchangeListener(data, user) {
        let container = document.querySelector(".exchange .messages");
        container.innerHTML = "";
        data.forEach(msg => {
            let mess = document.createElement("div");
            if (user == msg.utilisateur) {
                mess.classList.add("current");
            } else {
                mess.classList.add("interlocuteur");
            }
            mess.innerHTML = "<span>" + msg["dateIntMessage"].split(' ')[1] + "</span>";
            mess.innerHTML += "<span>" + msg["contentIntMessage"] + "</span>";
            container.appendChild(mess);

        })
    }
    document.getElementById("reloadMessage").addEventListener("click", function () {
        getMessage($("[data-exchangeID]").attr('data-exchangeID'), function (data) {
            exchangeListener(data, { var: user.ID_USER })
        }, 1000);
    })
    getMessage($("[data-exchangeID]").attr('data-exchangeID'), function (data) {
        exchangeListener(data, { var: user.ID_USER })
    }, 1000);
    function checkExchange() {
        var exchange = document.querySelectorAll("#nextEchange li")[0];
        var input = document.getElementById("internalMessage");
        if (exchange != undefined) {
            input.setAttribute("data-exchangeID", exchange.getAttribute("UUID"));
        }

    }
    const cmd = {
        trigger: document.getElementById("triggerMessage")
    };
    cmd.trigger?.addEventListener("click", function () {

        const message = document.querySelector("[data-exchangeID]");
        let exchange = message.getAttribute("data-exchangeID");
        sendMessage({ var: user.ID_USER }, exchange, message.value, (data) => {
            if (data.result == "ok") {
                message.value = "";
            }
        });
    })
    async function addMessage() {
        const message = document.querySelector("[data-exchangeID]");
        var m = message.value;
        if (m == "") {
            console.error("no messages !!");
            return;
        }
        var UUID = message.getAttribute("data-exchangeID");
        let messages = document.querySelector(".message");
        const url = `ajaxTest.php?act=addMessage&UUID=${UUID}`;
        data = {
            message: m
        }
        try {
            const ajax = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(data)
            });
            if (!ajax.ok) {
                throw new Error(`Erreur réseau : ${response.status}`);
            }
            const reponse = await ajax.json();
            if (reponse.result === "ok") {
                messages.innerHTML += `<span>${m}</span>`;
            }
        } catch (e) {
            console.error("Erreur lors de la récupération des données :", e);
        }

    }
    async function getNextExchange() {
        const url = "ajaxTest.php?act=nextExchange";
        try {
            const ajax = await fetch(url);
            const reponse = await ajax.json();
            if (reponse.result === "ok") {
                let ExchangeList = document.getElementById("nextEchange");
                ExchangeList.innerHTML = "";
                reponse.data.forEach(el => {
                    let listItem = document.createElement("li");
                    listItem.setAttribute("UUID", el.uuidExchange);
                    listItem.setAttribute("StatExchange", el.StateExchange);
                    listItem.innerHTML = "<b>" + el.HeureDebut + `</b><span>${el.Login}</span>`;
                    ExchangeList.appendChild(listItem);
                })
            }
        } catch (e) {
            console.error("Erreur lors de la récupération des données :", e);
        }
    }
    async function reserveSlot(slot) {
        const date = slot.getAttribute("data-date");
        const time = slot.getAttribute("data-hour");
        const url = "ajax.php?act=reserveExchange";
        try {
            let data = {
                date: date,
                heure: time
            }
            const ajax = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(data)
            });
            if (!ajax.ok) {
                throw new Error(`Erreur réseau : ${response.status}`);
            }
            const reponse = await ajax.json();
            if (reponse.result === "OK") {
                slot.classList.add("occuped");
            }
        } catch (e) {
            console.error("Erreur lors de la récupération des données :", e);
        }
    }
    async function getSlot(jour, date) {
        const url = "ajax.php?act=getSlotExchange&day=" + jour;
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Erreur réseau : ${response.status}`);
            }
            const reponse = await response.json();
            if (reponse.result === "OK") {
                var slotLits = document.getElementById("slots-list");
                slotLits.innerHTML = "";
                let slots = reponse.data.planningData.slots;
                slots.forEach(el => {
                    dateToCompare = new Date(`${date} ${el}`);
                    if (!dateToCompare.isPast()) {
                        let listItem = document.createElement("li");
                        let attr = el.replaceAll(":", "-");
                        listItem.setAttribute("data-date", date);
                        listItem.setAttribute("data-hour", attr + "-00");
                        listItem.innerText = el;
                        listItem.addEventListener("click", function () {
                            reserveSlot(this);
                        })
                        slotLits.appendChild(listItem);
                    }
                });
                let occuped = reponse.data.occupedSlots;
                occuped.forEach(el => {
                    let slotEl = document.querySelector(`[data-hour='${el.replaceAll(":", '-')}']`);
                    if (slotEl != undefined)
                        slotEl.classList.add("occuped");
                })
            }
        } catch (error) {
            console.error("Erreur lors de la récupération des données :", error);
            // Afficher un message utilisateur ou prendre une autre action
        }
    }
    document.querySelectorAll('.btn-day').forEach(button => {
        button.addEventListener('click', function () {
            document.querySelector(".exchange").style.display = "none";
            const selectedDay = this.getAttribute('data-day');
            const selectedDate = this.getAttribute('data-date');
            getSlot(selectedDay, selectedDate);
            // Mettre à jour l'affichage
            document.getElementById('selected-day').innerText = `${selectedDay} (${selectedDate})`;
            /*
                    // Charger les créneaux pour le jour sélectionné
                    const slotsData = JSON.parse(document.getElementById('slots-data').textContent); // Simule les données injectées
                    const slots = slotsData[selectedDay] || [];
            
                    const slotsList = document.getElementById('slots-list');
                    slotsList.innerHTML = ''; // Réinitialiser
            
                    slots.forEach(slot => {
                        const slotElement = document.createElement('li');
                        slotElement.innerHTML = `
                            <button class="btn btn-slot ${slot.STATE}" data-time="${slot.SLOT_TIME}" data-date="${slot.DATE}">
                                ${slot.SLOT_TIME} (${slot.STATE})
                            </button>
                        `;
                        slotsList.appendChild(slotElement);
                    });
            */
            // Afficher la section des créneaux
            document.querySelector('.agenda-slots').style.display = 'block';
        });
    });
</script>