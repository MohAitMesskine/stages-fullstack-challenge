# ✅ Résolution des Tickets du Backlog – Stages Fullstack Challenge

Ce dépôt contient l’ensemble des correctifs, optimisations et améliorations effectués pour résoudre les tickets du backlog du **Stages Fullstack Challenge**.  
L’objectif : corriger un maximum de tickets tout en respectant les bonnes pratiques **Laravel, sécurité web, performance et Git workflow**.

---

## 📊 Résumé Global des Tickets Résolus

| Catégorie        | Nombre | Statut | Difficulté |
|------------------|--------|--------|------------|
| 🐛 Bugs           | 4      | ✔️ Corrigés | ⭐ à ⭐⭐ |
| 🔒 Sécurité       | 3      | ✔️ Corrigés | ⭐⭐ à ⭐⭐⭐ |
| ⚡ Performance    | 3      | ✔️ Corrigés | ⭐⭐ à ⭐⭐⭐ |

**Total : 10 / 10 tickets résolus**  
➡️ **100% du backlog corrigé**

---

# 🧩 Détails des Résolutions

## 🐛 1. Bugs

### **BUG-001 — Recherche insensible aux accents**
- Problème : la recherche ne reconnaît pas les caractères accentués.
- Correction : normalisation + collation insensible aux accents (`utf8_general_ci`), `LOWER()` et traitement Laravel.
- Test : café / cafe • élève / eleve • été / ete.
![problem1](https://github.com/user-attachments/assets/be45d431-1c36-4b42-b900-2cebce99d022)

---

### **BUG-002 — Suppression du dernier commentaire**
- Cause : tentative d’accès à un index vide.
- Correction : ajout d’une vérification `if ($comment)` avant suppression.
- Test : suppression sur article avec 1 ou plusieurs commentaires.
![problem2](https://github.com/user-attachments/assets/dd6401da-8d36-439b-851a-42446a3e6698)

---

### **BUG-003 — Upload > 2MB (HTTP 413)**
- Limites détectées dans PHP, Laravel, Docker.
- Correction :
  - `upload_max_filesize` + `post_max_size`
  - Configuration Docker/Nginx
  - Validation Laravel
- Upload testé jusqu’à 10MB.
![problem3-validation](https://github.com/user-attachments/assets/32951f43-6140-4e1e-a876-a3297e1c5be5)

---

### **BUG-004 — Dates affichées en anglais**
- Correction :
  - `config/app.php` → locale=fr, timezone=Europe/Paris
  - Formatage Carbon
  - Conversion frontend
- Test : 12/09/2025 → “12 Septembre 2025”.
<img width="1209" height="230" alt="image" src="https://github.com/user-attachments/assets/803f3668-1f38-47d0-8c3f-cecbc3f1f149" />

---

## 🔒 2. Sécurité

### **SEC-001 — Mots de passe en clair**
- Implémentation du `bcrypt()`.
- Migration pour convertir les anciens mots de passe.
- Validation du login après hashage.
![security1](https://github.com/user-attachments/assets/d530ffe2-d424-4a73-a31c-da4e218d6b2b)

---

### **SEC-002 — Injection SQL dans la recherche**
- Requête SQL concaténée supprimée ❌
- Remplacement par :
  - requêtes préparées ✔️
  - Eloquent sécurisé ✔️
- Résistance testée : `' OR 1=1 --`, `UNION SELECT…`
![sql_injection](https://github.com/user-attachments/assets/77d39d26-db81-4a3e-8233-968026629c64)

---

### **SEC-003 — CORS ouvert + XSS dans commentaires**
- Restriction CORS aux domaines autorisés.
- Nettoyage des commentaires (sanitize).
- Suppression des injections JavaScript possibles.
![test_xss](https://github.com/user-attachments/assets/99074ce8-4f77-47fc-ae7d-73c94d95c1b0)

---

## ⚡ 3. Performance

### **PERF-001 — Problème N+1**
- Correction : `Article::with(['author', 'comments'])`.
- Résultat : **101 requêtes → 3 requêtes**.
![performance ](https://github.com/user-attachments/assets/fd8537aa-051a-4d75-bb47-c338d1a2c25f)

---

### **PERF-002 — Optimisation d’images**
- Compression automatique backend.
- Redimensionnement 1200px max.
- Conversion WebP (bonus).
![performance](https://github.com/user-attachments/assets/0ffb53b9-b03d-49bf-9f34-3830e57c6dd3)

---

### **PERF-003 — Cache des pages**
- Mise en place de `Cache::remember()`.
- Expiration configurable.
- Résultat : gain de vitesse sur pages très consultées.
![performanche_cashe](https://github.com/user-attachments/assets/32a1e8ee-e703-40b0-952a-4d42bb4c9074)

---

# 🔧 Workflow Git Utilisé

