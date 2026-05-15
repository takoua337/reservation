
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription — Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="auth-wrapper">
  <div style="position:absolute;top:2rem;left:2rem;">
    <a href="index.html" class="auth-logo text-decoration-none">Grand Élysée</a>
  </div>

  <div class="auth-card">
    <p class="section-overline mb-1">Rejoignez-nous</p>
    <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--cream);margin-bottom:0.25rem;">
      Créer un compte
    </h2>
    <div class="divider-gold"></div>
    <form action="register-login.php" method="post">
      <div class="row g-3">
        <div class="col-6">
          <label class="form-label">Prénom</label>
          <input type="text" class="form-control" name="prenom" required/>
        </div>
        <div class="col-6">
          <label class="form-label">Nom</label>
          <input type="text" class="form-control" name="nom" required/>
        </div>
        <div class="col-12">
          <label class="form-label">Adresse email</label>
          <input type="email" class="form-control" name="email" required/>
        </div>
        <div class="col-12">
          <label class="form-label">Téléphone</label>
          <input type="tel" class="form-control" name="telephone"/>
        </div>
         <div class="col-12">
  <label class="form-label">Role</label>
  <div class="input-group">
    <select name="role" class="form-control" required>
      <option value="">Choisir un rôle</option>
      <option value="admin">Admin</option>
      <option value="client">Client</option>
    </select>
  </div>
</div>
        <div class="col-12">
          <label class="form-label">Mot de passe</label>
          <div class="input-group">
            <input type="password" class="form-control" id="pwd" name="mot_passe" required/>
            <button type="button" class="btn" style="background:var(--dark3);border:1px solid rgba(201,168,76,0.25);color:var(--muted);" onclick="togglePwd('pwd')">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
   <!--
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" required
              style="background:var(--dark3);border-color:rgba(201,168,76,0.3);"/>
            <label class="form-check-label" for="terms" style="font-size:0.82rem;color:var(--muted);">
             J'accepte les <a href="#" style="color:var(--gold);">conditions d'utilisation</a>
            </label>
          </div>
        </div>
        -->
        <div class="col-12 mt-2">
          <button type="submit" class="btn btn-gold w-100" name="enregistrer">Créer mon compte</button>
        </div>
      </div>
    </form>

    <p style="text-align:center;margin-top:1.5rem;font-size:0.85rem;color:var(--muted);">
      Déjà membre ? <a href="login.php" style="color:var(--gold);">Se connecter</a>
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePwd(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
  }
</script>
</body>
</html>