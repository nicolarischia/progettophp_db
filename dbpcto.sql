-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mag 27, 2025 alle 17:05
-- Versione del server: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dbpcto`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `aziende`
--

CREATE TABLE IF NOT EXISTS `aziende` (
  `idAzienda` int(2) NOT NULL AUTO_INCREMENT,
  `regioneSociale` varchar(30) NOT NULL,
  `partitaIva` varchar(30) NOT NULL,
  `numCivico` int(2) NOT NULL,
  `telefono` int(10) NOT NULL,
  `numPartiteIva` char(11) DEFAULT NULL,
  PRIMARY KEY (`idAzienda`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dump dei dati per la tabella `aziende`
--

INSERT INTO `aziende` (`idAzienda`, `regioneSociale`, `partitaIva`, `numCivico`, `telefono`, `numPartiteIva`) VALUES
(2, ' Azienda Esempio S.r.l.', '63473673653', 10, 212345678, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `classi`
--

CREATE TABLE IF NOT EXISTS `classi` (
  `nomeClasse` char(4) NOT NULL,
  `anno` int(1) NOT NULL,
  `specializzazione` varchar(30) NOT NULL,
  `idDocente` int(2) NOT NULL,
  PRIMARY KEY (`nomeClasse`),
  KEY `idDocente` (`idDocente`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `classi`
--

INSERT INTO `classi` (`nomeClasse`, `anno`, `specializzazione`, `idDocente`) VALUES
('4AIA', 4, 'Informatica', 1),
('5AIA', 5, 'Informatica', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `docentireferenti`
--

CREATE TABLE IF NOT EXISTS `docentireferenti` (
  `idDocente` int(2) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(30) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  PRIMARY KEY (`idDocente`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `docentireferenti`
--

INSERT INTO `docentireferenti` (`idDocente`, `cognome`, `nome`, `email`) VALUES
(1, 'Tomeucci', 'Antonella', 'antonella.tomeucci@ittterni.org');

-- --------------------------------------------------------

--
-- Struttura della tabella `stage`
--

CREATE TABLE IF NOT EXISTS `stage` (
  `idStage` int(4) NOT NULL AUTO_INCREMENT,
  `argomento` varchar(20) NOT NULL,
  `modoSvolgimento` enum('Esercizio pratico','Autoapprendimento','Apprendimento assistito') NOT NULL,
  `dataInizio` date NOT NULL,
  `dataFine` date DEFAULT NULL,
  `oreSettoreSvolte` int(3) NOT NULL,
  `valutazione` int(2) DEFAULT NULL,
  `idStudente` int(3) NOT NULL,
  `idAzienda` int(2) NOT NULL,
  PRIMARY KEY (`idStage`),
  KEY `idStudente` (`idStudente`),
  KEY `idAzienda` (`idAzienda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `studenti`
--

CREATE TABLE IF NOT EXISTS `studenti` (
  `idStudente` int(3) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(30) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `dataDiNascita` date NOT NULL,
  `email` varchar(60) NOT NULL,
  `classe` char(4) NOT NULL,
  PRIMARY KEY (`idStudente`),
  KEY `classe` (`classe`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dump dei dati per la tabella `studenti`
--

INSERT INTO `studenti` (`idStudente`, `cognome`, `nome`, `dataDiNascita`, `email`, `classe`) VALUES
(2, 'Bianchi', 'Mario', '2007-05-02', 'mariobianchi@gmail.com', '5AIA'),
(4, 'Neri', 'Mario', '2000-03-04', 'mrossi@gmail.com', '5AIA'),
(5, 'Rossi', 'Mario', '2000-05-04', 'mario@prova.com', '4AIA');

-- --------------------------------------------------------

--
-- Struttura della tabella `tutoraziendali`
--

CREATE TABLE IF NOT EXISTS `tutoraziendali` (
  `idTutor` int(2) NOT NULL AUTO_INCREMENT,
  `cognome` varchar(30) NOT NULL,
  `nome` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  `telefono` int(10) NOT NULL,
  `idAzienda` int(2) NOT NULL,
  PRIMARY KEY (`idTutor`),
  KEY `idAzienda` (`idAzienda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE IF NOT EXISTS `utenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `email`, `password`) VALUES
(1, 'nicolarischia@gmail.com', '$2y$10$9Vp7PzLmMJ5gGyrnRyNGqeM.NAoTDBFjlnd8LcX/.HB8DLsnF/WKi'),
(5, 'nicolarischia1@gmail.com', '$2y$10$wz0FM65eJHi3RcS5cN9bfOVq0qpU4DU2F.LdC1NOYzjqwTAPscP6S'),
(10, 'admin@php.com', '$2y$10$sMjqnKxbbMQqg/yYNztJXOQ1bFhPhf1Wr2Kf2nYK./T9YeiQ50Mam'),
(11, 'nicola@gmail.com', '$2y$10$rR6EAHRUaCzGriKp4BHf9OxZIgMWNZsRhTfc2AoSF04E5bLVyONe6');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `classi`
--
ALTER TABLE `classi`
  ADD CONSTRAINT `classi_ibfk_1` FOREIGN KEY (`idDocente`) REFERENCES `docentireferenti` (`idDocente`);

--
-- Limiti per la tabella `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `stage_ibfk_1` FOREIGN KEY (`idStudente`) REFERENCES `studenti` (`idStudente`),
  ADD CONSTRAINT `stage_ibfk_2` FOREIGN KEY (`idAzienda`) REFERENCES `aziende` (`idAzienda`);

--
-- Limiti per la tabella `studenti`
--
ALTER TABLE `studenti`
  ADD CONSTRAINT `studenti_ibfk_1` FOREIGN KEY (`classe`) REFERENCES `classi` (`nomeClasse`);

--
-- Limiti per la tabella `tutoraziendali`
--
ALTER TABLE `tutoraziendali`
  ADD CONSTRAINT `tutoraziendali_ibfk_1` FOREIGN KEY (`idAzienda`) REFERENCES `aziende` (`idAzienda`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
