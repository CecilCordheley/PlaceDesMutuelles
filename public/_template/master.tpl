<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <!-- <link href="_css/styles.css" rel="stylesheet" />-->
  <link rel="stylesheet" href="_css/styles.css">
  <link rel="shortcut icon" href="img/MUTUELLE/default.png">
  <script src="_js/alert.js"></script>

  <title>Forum des Mutuelles</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Acceuil</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="./SujetAll">Voir les sujets</a>
          </li>

          {:IF {var:isConnect}=1}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Sujets
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="NouveauSujet">Ouvrir un sujet</a></li>
              <li><a class="dropdown-item" href="MesSujets">Voir mes sujets</a></li>
              <li><a href="#" class="dropdown-item">Mes Favoris</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Ma Mutuelle
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="MonCompte">Actualité</a></li>
              <li><a class="dropdown-item" href="#">FAQ</a></li>
              <li><a href="EventTest" class="dropdown-item">Événements</a></li>
              <li><a class="dropdown-item" href="Contact">contacter {var:user.NOM_ORGANISME}</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Mon Compte
            </a>

            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="MonCompte">Voir mon compte</a></li>

              <li><a href="activite" class="dropdown-item">Historique d'activité</a></li>
              <li><a class="dropdown-item" href="#">Aide</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="deconnexion">Deconnexion</a></li>
            </ul>
          </li>

          {:ELSE:}
          <li class="nav-item">
            <a class="nav-link" href="connexion">connexion</a>
          </li>
          {:/IF}
          {:IF {var:user.TYPE_UTILISATEUR}=2}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Interface de Gestion
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="UserGestion">Gestion des utilisateurs</a></li>
              <li><a class="dropdown-item" href="#">Gestion des messages</a></li>
              <li><a class="dropdown-item" href="SujetGestion">Gestion des sujets</a></li>
              <li><a class="dropdown-item" href="ExchangeGestion">Gestion des echanges</a></li>
            </ul>
          </li>
          {:/IF}
        </ul>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
      <div class="text-center text-white">
        <h1 class="display-4 fw-bolder">Forum des Mutuelles</h1>
        <p class="lead fw-normal text-white-50 mb-0">La place des adhérents et des mutuelles</p>
      </div>
    </div>
  </header>
  <!-- Section-->
  <section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
      {var:MainContent}
    </div>
  </section>
  <!-- Footer-->
  <footer class="py-5 bg-dark">
    <div class="container">
      <p class="m-0 text-center text-white">Copyright &copy; Cecil Cordheley {var:year}</p>
      {:IF {var:user.TYPE_UTILISATEUR}=4}
      <ul class="nav nav-underline">
        <li class="nav-item">
          <a class="nav-link {var:activPage.USER}" aria-current="page" href="{:racine}UserGestion">UTILSATEURS</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">MUTUELLES</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {var:activPage.SUJET}" href="{:racine}SujetGestion">SUJETS</a>
        </li>
      </ul>
      {:/IF}
    </div>
  </footer>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="_js/scripts.js"></script>
</body>

</html>