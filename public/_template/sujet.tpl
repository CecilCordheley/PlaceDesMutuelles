<a href="SujetAll">Voir les sujets</a>
<h2>{var:SUJET.LIB_SUJET} <span>{var:SUJET.DATE_SUJET}</span></h2>
<main>
    {view:allMessage}
</main>
    {:IF {var:SUJET.DATE_CLOTURE}=NULL}
    <form onsubmit="return checkMessage()" name="postMessage" class="row g-3" method="POST" action="POSTMessage_{var:SUJET.ID_SUJET}">
        <div><label for="messageInput" class="form-label">Message : </label><textarea maxlength="255"
            class="form-control" id="messageInput" name="message" rows="3"></textarea></div>
        <div><button type="submit" class="btn btn-primary mb-3">Poster</button></div> <span
           id="lengthMessage">0/255</span>
        </form>
    {:ELSE:}
        <p>Le sujet est clos</p>
    {:/IF}
    {:IF {var:SUJET.ID_USER}={var:user.ID_USER}}
        <a>Clore le sujet</a>
    {:/IF}

