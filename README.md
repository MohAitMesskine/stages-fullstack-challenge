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
<img width="722" height="471" alt="image" src="https://github.com/user-attachments/assets/78e23f0b-cafd-4732-9187-284d707d59f3" />


---

### **SEC-003 — CORS ouvert + XSS dans commentaires**
- Restriction CORS aux domaines autorisés.
- Nettoyage des commentaires (sanitize).
- Suppression des injections JavaScript possibles.
<img width="539" height="632" alt="image" src="https://github.com/user-attachments/assets/5032c087-f4c0-453c-9123-4869d492e816" />


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

<img width="1222" height="478" alt="image" src="https://github.com/user-attachments/assets/f5d09ee8-fffb-4218-aaff-2cb59863100c" />

---

### **PERF-003 — Cache des pages**
- Mise en place de `Cache::remember()`.
- Expiration configurable.
- Résultat : gain de vitesse sur pages très consultées.
<img width="608" height="484" alt="image" src="https://github.com/user-attachments/assets/ae8b7f65-2e5e-4ba3-922d-ae9f17ed2e6b" />


---

# 🔧 Workflow Git Utilisé

