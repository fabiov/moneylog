--
-- Struttura della tabella `accantonati`
--

DROP TABLE IF EXISTS `accantonati`;
CREATE TABLE IF NOT EXISTS `accantonati` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `valuta` date NOT NULL,
  `importo` float(8,2) DEFAULT NULL,
  `descrizione` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `spese`
--

DROP TABLE IF EXISTS `spese`;
CREATE TABLE IF NOT EXISTS `spese` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valuta` date NOT NULL,
  `id_categoria` int(10) NOT NULL,
  `importo` float(8,2) DEFAULT NULL,
  `descrizione` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `variabili`
--

DROP TABLE IF EXISTS `variabili`;
CREATE TABLE IF NOT EXISTS `variabili` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segno` tinyint(4) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valore` float(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
