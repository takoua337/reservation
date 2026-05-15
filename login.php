<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion — Grand Élysée</title>
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
    <p class="section-overline mb-1">Bienvenue</p>
    <h2 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--cream);margin-bottom:0.25rem;">
      Se connecter
    </h2>
    <div class="divider-gold"></div>

    <form action="register-login.php" method="post">
      <div class="mb-3">
        <label class="form-label">Adresse email</label>
        <input type="email" class="form-control" name="email" required/>
      </div>
      <div class="mb-4">
        <div class="d-flex justify-content-between">
          <label class="form-label">Mot de passe</label>
          <a href="#" style="font-size:0.75rem;color:var(--gold);">Mot de passe oublié ?</a>
        </div>
        <div class="input-group">
          <input type="password" class="form-control" id="pwd" name="mot_passe" required/>
          <button type="button" class="btn" style="background:var(--dark3);border:1px solid rgba(201,168,76,0.25);color:var(--muted);" onclick="togglePwd()">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>
      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="remember"
          style="background:var(--dark3);border-color:rgba(201,168,76,0.3);"/>
        <label class="form-check-label" for="remember" style="font-size:0.82rem;color:var(--muted);">
          Se souvenir de moi
        </label>
      </div>
      <button type="submit" class="btn btn-gold w-100" name= "login">Se connecter</button>
    </form>

    <p style="text-align:center;margin-top:1.5rem;font-size:0.85rem;color:var(--muted);">
      Pas encore de compte ? <a href="register.php" style="color:var(--gold);">S'inscrire</a>
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePwd() {
    const el = document.getElementById('pwd');
    el.type = el.type === 'password' ? 'text' : 'password';
  }
</script>
</body>
</html>