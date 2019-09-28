CREATE TABLE `mismatch_topic` (
  `topic_id` INT AUTO_INCREMENT,
  `name` VARCHAR(48),
  `category` VARCHAR(48),
  `category_id` INT,
  PRIMARY KEY (`topic_id`)
);

INSERT INTO `mismatch_topic` VALUES (1, 'Tatuagens', 'Aparência', 1);
INSERT INTO `mismatch_topic` VALUES (2, 'Correntes de ouro', 'Aparência', 1);
INSERT INTO `mismatch_topic` VALUES (3, 'Piercings corporais', 'Aparência', 1);
INSERT INTO `mismatch_topic` VALUES (4, 'Botas de Cowboy', 'Aparência', 1);
INSERT INTO `mismatch_topic` VALUES (5, 'Cabelo comprido', 'Appearance', 1);
INSERT INTO `mismatch_topic` VALUES (6, 'Reality Show', 'Entretenimento', 2);
INSERT INTO `mismatch_topic` VALUES (7, 'Luta profissional', 'Entretenimento', 2);
INSERT INTO `mismatch_topic` VALUES (8, 'Filmes de terror', 'Entretenimento', 2);
INSERT INTO `mismatch_topic` VALUES (9, 'Música orquestrada', 'Entretenimento', 2);
INSERT INTO `mismatch_topic` VALUES (10, 'Ópera', 'Entretenimento', 2);
INSERT INTO `mismatch_topic` VALUES (11, 'Sushi', 'Comida', 3);
INSERT INTO `mismatch_topic` VALUES (12, 'Spam', 'Comida', 3);
INSERT INTO `mismatch_topic` VALUES (13, 'Comida apimentada', 'Comida', 3);
INSERT INTO `mismatch_topic` VALUES (14, 'Manteiga de amendoim & sanduíches de banana', 'Comida', 3);
INSERT INTO `mismatch_topic` VALUES (15, 'Martinis', 'Comida', 3);
INSERT INTO `mismatch_topic` VALUES (16, 'Howard Stern', 'Pessoa', 4);
INSERT INTO `mismatch_topic` VALUES (17, 'Bill Gates', 'Pessoa', 4);
INSERT INTO `mismatch_topic` VALUES (18, 'Barbara Streisand', 'Pessoa', 4);
INSERT INTO `mismatch_topic` VALUES (19, 'Hugh Hefner', 'Pessoa', 4);
INSERT INTO `mismatch_topic` VALUES (20, 'Martha Stewart', 'Pessoa', 4);
INSERT INTO `mismatch_topic` VALUES (21, 'Yoga', 'Atividades', 5);
INSERT INTO `mismatch_topic` VALUES (22, 'Levantamento de peso', 'Atividades', 5);
INSERT INTO `mismatch_topic` VALUES (23, 'Cubos-mágicos', 'Atividades', 5);
INSERT INTO `mismatch_topic` VALUES (24, 'Karaokê', 'Atividades', 5);
INSERT INTO `mismatch_topic` VALUES (25, 'Caminhada', 'Atividades', 5);
