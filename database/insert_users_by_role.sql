-- =============================================================================
-- Requête SQL pour créer 1 utilisateur pour chaque rôle (4 rôles)
-- =============================================================================
-- Mot de passe pour chaque utilisateur :
--   admin     -> admin123
--   manager   -> manager123
--   employe   -> employe123
--   client    -> client123

-- =============================================================================
-- SUPPRIMER LES ANCIENS UTILISATEURS (si besoin de recommencer)
-- =============================================================================
DELETE FROM employes WHERE user_id IN (SELECT id FROM users WHERE email IN (
    'admin@usine.local', 'manager@usine.local', 'employe@usine.local', 'client@usine.local'
));
DELETE FROM users WHERE email IN (
    'admin@usine.local', 'manager@usine.local', 'employe@usine.local', 'client@usine.local'
);

-- =============================================================================
-- INSÉRER LES 4 UTILISATEURS AVEC MOT DE PASSE DIFFÉRENT
-- =============================================================================
-- Hash des mots de passe générés avec password_hash()
INSERT INTO users (nom, email, password, role, actif) VALUES
('Administrateur', 'admin@usine.local', '$2y$10$9Lcqe2syNvcw3O9pQjq94.RhOr5Wlxnf8dWwRHmX7pDGj2Mhkkpi.', 'admin', 1),
('Manager', 'manager@usine.local', '$2y$10$JUxnat3892.sxQeXL0jov.czQYUfbzkJlZ8Y8yO/ZB2/nvDEve7XC', 'manager', 1),
('Employé', 'employe@usine.local', '$2y$10$c1HFNlXUjVaWQw7yvzj/k.RSkhh.PNhBw5l215I9QiQHPMqgnPUwu', 'employe', 1),
('Client', 'client@usine.local', '$2y$10$zFC8I31qHqANI.jPRUOG.eo2hfoEngIn6rTGCc9hpp7BFegbeqbC.', 'client', 1);

-- =============================================================================
-- CRÉER L'ENREGISTREMENT EMPLOYÉ (pour le rôle employe)
-- =============================================================================
INSERT INTO employes (user_id, poste, salaire_base, telephone, date_embauche) 
SELECT id, 'Technicien', 1500.00, '12345678', '2024-01-15' 
FROM users WHERE email = 'employe@usine.local';

-- =============================================================================
-- RÉSUMÉ DES COMPTES CRÉÉS
-- =============================================================================
-- | Rôle     | Email                  | Mot de passe |
-- |----------|------------------------|--------------|
-- | admin    | admin@usine.local      | admin123     |
-- | manager  | manager@usine.local    | manager123   |
-- | employe  | employe@usine.local    | employe123   |
-- | client   | client@usine.local     | client123    |
