-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2023 at 05:55 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carp`
--

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`kode`, `nama`) VALUES
('BRC2307001', 'SUB'),
('BRC2307002', 'JKT');

-- --------------------------------------------------------

--
-- Table structure for table `carp`
--

CREATE TABLE `carp` (
  `kode` varchar(10) NOT NULL,
  `created_date` date DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `initiator_user_kode` varchar(10) DEFAULT NULL,
  `initiator_divisi_kode` varchar(10) DEFAULT NULL,
  `initiator_branch_kode` varchar(10) DEFAULT NULL,
  `recipient_user_kode` varchar(10) DEFAULT NULL,
  `recipient_divisi_kode` varchar(10) DEFAULT NULL,
  `recipient_branch_kode` varchar(10) DEFAULT NULL,
  `verified_user_kode` varchar(10) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `effectiveness` varchar(10) DEFAULT NULL,
  `status_date` date DEFAULT NULL,
  `stage` varchar(15) DEFAULT NULL,
  `status` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `carp`
--

INSERT INTO `carp` (`kode`, `created_date`, `kategori`, `initiator_user_kode`, `initiator_divisi_kode`, `initiator_branch_kode`, `recipient_user_kode`, `recipient_divisi_kode`, `recipient_branch_kode`, `verified_user_kode`, `due_date`, `effectiveness`, `status_date`, `stage`, `status`) VALUES
('CARP00001', '2023-07-08', 'internal audit findings, non conformity', 'USR2307001', 'DIV2307001', 'BRC2307001', 'USR2307013', 'DIV2307006', 'BRC2307002', '', '2023-08-08', NULL, '2023-07-08', 'Open', 'Open'),
('CARP00002', '2023-07-08', 'internal audit findings, non conformity', 'USR2307002', 'DIV2307002', 'BRC2307001', 'USR2307014', 'DIV2307004', 'BRC2307001', 'USR2307002', '2023-08-08', NULL, '2023-07-08', 'Closed', 'Closed'),
('CARP00003', '2023-07-08', 'internal audit findings, non conformity', 'USR2307002', 'DIV2307002', 'BRC2307001', 'USR2307015', 'DIV2307006', 'BRC2307001', '', '2023-08-08', NULL, '2023-07-08', 'Open', 'Open'),
('CARP00004', '2023-07-08', 'oportunity for improvement', 'USR2307002', 'DIV2307002', 'BRC2307001', 'USR2307004', 'DIV2307003', 'BRC2307001', 'USR2307002', '2023-08-08', NULL, '2023-07-08', 'Closed', 'Closed'),
('CARP00005', '2023-07-08', 'internal audit findings, non conformity', 'USR2307001', 'DIV2307001', 'BRC2307001', 'USR2307012', 'DIV2307004', 'BRC2307001', '', '2023-08-08', NULL, '2023-07-08', 'Voided', 'Canceled');

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`kode`, `nama`) VALUES
('DIV2307001', 'SALES'),
('DIV2307002', 'IC'),
('DIV2307003', 'COMMERCIAL'),
('DIV2307004', 'KEY ACCOUNT'),
('DIV2307005', 'HSE'),
('DIV2307006', 'OPERATION'),
('DIV2307007', 'MANAGEMENT'),
('DIV2307008', 'CR'),
('DIV2307009', 'TRUCKING'),
('DIV2307010', 'CC'),
('DIV2307011', 'HR&GA'),
('DIV2307012', 'PROCUREMENT'),
('DIV2307013', 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`kode`, `nama`) VALUES
('USR2307001', 'WANDA MAXIMOFF'),
('USR2307002', 'NATALIA ALIANOVNA ROMANOVA'),
('USR2307003', 'BARBARA \"BOBBI\" MORSE'),
('USR2307004', 'NAMORITA \"NITA\" PRENTISS'),
('USR2307005', 'JOHN JONAH JAMESON'),
('USR2307006', 'JANET VAN DYNE'),
('USR2307007', 'STEPHEN VINCENT STRANGE'),
('USR2307008', 'ELEKTRA NATCHIOS'),
('USR2307009', 'CAMELLIA'),
('USR2307010', 'CATHRINE MORA'),
('USR2307011', 'MATT MURDOCK'),
('USR2307012', 'DEREK MORGAN'),
('USR2307013', 'ELLIE CAMACHO'),
('USR2307014', 'FIN CASEY'),
('USR2307015', 'GAMORA'),
('USR2307016', 'CLINT BARTON'),
('USR2307017', 'PATSY WALKER'),
('USR2307018', 'ROBERT BRUCE BANNER'),
('USR2307019', 'CAIN MARKO'),
('USR2307020', 'JEAN GREY'),
('USR2307021', 'MILES BULLOCK'),
('USR2307022', 'JAMES MADROX');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `carp`
--
ALTER TABLE `carp`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`kode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
