<div class="row g-3 align-items-center">
  <div class="col-3">
    <div class="dropdown">
      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Themes
      </button>
      <ul class="dropdown-menu p-3" style="min-width: 250px;">
        <li>
          <div class="form-check">
            <input name="ThemeFilter" class="form-check-input" type="checkbox" value="NULL" id="NULL">
            <label class="form-check-label" for="NULL">
              Tout les themes
            </label>
          </div>
        </li>
        {LOOP:catFilt}
        <li>
          <div class="form-check">
            <input name="ThemeFilter" class="form-check-input" type="checkbox" value="{#ID_THEME#}" id="{#ID_THEME#}">
            <label class="form-check-label" for="{#ID_THEME#}">
              {#LIB_THEME#}
            </label>
          </div>
        </li>
        {/LOOP}
      </ul>
    </div>
  </div>
  <div class="col-3">
    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      autre filtres
    </button>
    <ul class="dropdown-menu p-3" style="min-width: 250px;">
      {:IF {var:user.ID_USER}! }
      <li>
        <div class="form-check">
          <input name="filter" class="form-check-input" type="checkbox" id="excludeSub">
          <label class="form-check-label" for="OpenSubject">
            Sujet de {var:user.NOM_ORGANISME}
          </label>
        </div>
      </li>
      {:/IF}
      <li>
        <div class="form-check">
          <input name="filter" class="form-check-input" type="checkbox" id="OpenSubject">
          <label class="form-check-label" for="OpenSubject">
            Sujet ouverts
          </label>
        </div>
      </li>
      <li>
        <div class="form-check">
          <input name="filter" class="form-check-input" type="checkbox" id="CloseSubject">
          <label class="form-check-label" for="CloseSubject">
            Sujet clos
          </label>
        </div>
      </li>
    </ul>
  </div>

</div>
<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
  {LOOP:topic}
  <div class="subject col mb-5" theme="{#ID_THEME#}" closed="{#CLOSED#}">
    <div class="card h-100">

      <!-- topic details-->
      <div class="card-body p-4">
        <div class="text-center">
          <!-- topic name-->
          <h5 class="fw-bolder">{#LIB_SUJET#}</h5>
          <span class="text-muted">{#THEME#}</span>
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">Nombre de message : {#NBMESSAGE#}</li>
        </ul>

        <p class="card-text">{#LASTMESSAGE#}</p>
      </div>
      <!-- Topic actions-->
      {:IF {var:user.ID_USER}! }
      <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
        <div class="text-center"><a class="btn btn-outline-dark mt-auto"
            href="sujet-{#ID_SUJET#}-{#URL_SUJET#}.html">Voir ce topic</a>
        </div>
      </div>
      {:ELSE:}
      <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
        <div class="text-center">
          <a class="btn btn-outline-dark mt-auto needConnect" href="#">Voir ce topic</a>
        </div>
      </div>
      {:/IF}
    </div>
  </div>
  {/LOOP}
  {view:messageByOrga}
</div>
{:IF {var:user.ID_USER}! }
<h2>Evénements</h2>
<div id="carouselEvent" class="carousel slide">
  <div class="carousel-inner">
    {LOOP:orgaEvent}
    <div class="carousel-item">
      <h3>{#titreEvent#} <span>{#dateEvent#}</span></h3>
      <p>{#descEvent#}</p>
    </div>
    {/LOOP}
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselEvent" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselEvent" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<script>
  document.querySelectorAll("#carouselEvent .carousel-item")[0].classList.add("active");
</script>
{:/IF}