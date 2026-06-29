-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 03, 2023 at 07:37 AM
-- Server version: 5.7.17
-- PHP Version: 8.1.2

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `necoyoad_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_address`
--

CREATE TABLE `nts8sd4fd_address` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `company` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_2` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `city` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `street` varchar(250) NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_balance`
--

CREATE TABLE `nts8sd4fd_balance` (
  `balance_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `type` varchar(3) NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `description` text NOT NULL,
  `amount_available` decimal(10,4) NOT NULL,
  `amount_blocked` decimal(10,4) NOT NULL,
  `amount_total` decimal(10,4) NOT NULL,
  `currency_code` varchar(50) NOT NULL,
  `currency_value` decimal(10,4) NOT NULL,
  `currency_title` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_bank`
--

CREATE TABLE `nts8sd4fd_bank` (
  `bank_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `image` varchar(250) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_bank_account`
--

CREATE TABLE `nts8sd4fd_bank_account` (
  `bank_account_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `number` varchar(250) NOT NULL,
  `accountholder` varchar(250) NOT NULL,
  `type` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rif` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_banner`
--

CREATE TABLE `nts8sd4fd_banner` (
  `banner_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `jquery_plugin` varchar(150) NOT NULL,
  `params` text NOT NULL,
  `publish_date_start` date NOT NULL,
  `publish_date_end` date NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_banner_item`
--

CREATE TABLE `nts8sd4fd_banner_item` (
  `banner_item_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  `link` varchar(250) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_campaign`
--

CREATE TABLE `nts8sd4fd_campaign` (
  `campaign_id` int(11) NOT NULL,
  `newsletter_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `subject` varchar(70) NOT NULL,
  `from_name` varchar(70) NOT NULL,
  `from_email` varchar(100) NOT NULL,
  `replyto_email` varchar(100) NOT NULL,
  `trace_email` int(1) NOT NULL,
  `trace_click` int(1) NOT NULL,
  `embed_image` int(1) NOT NULL,
  `repeat` varchar(50) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_campaign_contact`
--

CREATE TABLE `nts8sd4fd_campaign_contact` (
  `campaign_contact_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `task_queue_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_campaign_link`
--

CREATE TABLE `nts8sd4fd_campaign_link` (
  `campaign_link_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `url` varchar(250) NOT NULL,
  `redirect` varchar(250) NOT NULL,
  `link` varchar(250) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_campaign_link_stat`
--

CREATE TABLE `nts8sd4fd_campaign_link_stat` (
  `campaign_link_stat_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `link` varchar(250) NOT NULL,
  `store_url` varchar(250) NOT NULL,
  `server` text NOT NULL,
  `session` text NOT NULL,
  `request` text NOT NULL,
  `ref` varchar(250) NOT NULL,
  `browser` varchar(150) NOT NULL,
  `browser_version` varchar(50) NOT NULL,
  `os` varchar(150) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_campaign_stat`
--

CREATE TABLE `nts8sd4fd_campaign_stat` (
  `campaign_stat_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `store_url` varchar(250) NOT NULL,
  `server` text NOT NULL,
  `session` text NOT NULL,
  `request` text NOT NULL,
  `ref` varchar(250) NOT NULL,
  `browser` varchar(150) NOT NULL,
  `browser_version` varchar(50) NOT NULL,
  `os` varchar(150) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_category`
--

CREATE TABLE `nts8sd4fd_category` (
  `category_id` int(11) NOT NULL,
  `object_type` varchar(255) CHARACTER SET utf8 DEFAULT 'product',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `viewed` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_contact`
--

CREATE TABLE `nts8sd4fd_contact` (
  `contact_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `date_deleted` datetime NOT NULL,
  `is_active` int(1) NOT NULL,
  `is_deleted` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_contact_list`
--

CREATE TABLE `nts8sd4fd_contact_list` (
  `contact_list_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` int(11) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_contact_to_list`
--

CREATE TABLE `nts8sd4fd_contact_to_list` (
  `contact_to_list_id` int(11) NOT NULL,
  `contact_list_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_country`
--

CREATE TABLE `nts8sd4fd_country` (
  `country_id` int(11) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `iso_code_2` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `iso_code_3` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `address_format` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_coupon`
--

CREATE TABLE `nts8sd4fd_coupon` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(24) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `type` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `discount` decimal(15,4) NOT NULL,
  `logged` int(1) NOT NULL,
  `shipping` int(1) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `uses_total` int(11) NOT NULL,
  `uses_customer` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_coupon_category`
--

CREATE TABLE `nts8sd4fd_coupon_category` (
  `coupon_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_coupon_history`
--

CREATE TABLE `nts8sd4fd_coupon_history` (
  `coupon_history_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_coupon_product`
--

CREATE TABLE `nts8sd4fd_coupon_product` (
  `coupon_product_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_currency`
--

CREATE TABLE `nts8sd4fd_currency` (
  `currency_id` int(11) NOT NULL,
  `code` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `symbol_left` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `symbol_right` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `decimal_place` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` float(15,8) NOT NULL,
  `status` int(1) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_customer`
--

CREATE TABLE `nts8sd4fd_customer` (
  `customer_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL,
  `referenced_by` int(11) NOT NULL,
  `firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(96) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `telephone` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sex` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `cart` text CHARACTER SET utf8 COLLATE utf8_bin,
  `newsletter` int(1) NOT NULL DEFAULT '0',
  `rif` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `company` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `activation_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `birthday` date NOT NULL,
  `congrats` int(1) NOT NULL DEFAULT '1',
  `status` int(1) NOT NULL,
  `banned` int(1) NOT NULL,
  `approved` int(1) NOT NULL DEFAULT '0',
  `complete` int(1) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_customer_group`
--

CREATE TABLE `nts8sd4fd_customer_group` (
  `customer_group_id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `params` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_description`
--

CREATE TABLE `nts8sd4fd_description` (
  `description_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL COMMENT 'relation with object''s table',
  `object_type` varchar(100) NOT NULL COMMENT 'type of the object in the relation',
  `language_id` int(11) NOT NULL COMMENT 'relation with language''s table',
  `title` varchar(255) NOT NULL COMMENT 'the title of the content',
  `description` text COMMENT 'the rich or html content',
  `seo_title` varchar(60) DEFAULT NULL COMMENT 'SEO title',
  `meta_description` varchar(160) DEFAULT NULL COMMENT 'SEO description or resume of the content',
  `meta_keywords` varchar(255) DEFAULT NULL COMMENT 'SEO keywords',
  `params` text COMMENT 'anything',
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_download`
--

CREATE TABLE `nts8sd4fd_download` (
  `download_id` int(11) NOT NULL,
  `filename` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `mask` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `remaining` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_extension`
--

CREATE TABLE `nts8sd4fd_extension` (
  `extension_id` int(11) NOT NULL,
  `type` varchar(32) COLLATE utf8_bin NOT NULL,
  `app` varchar(50) COLLATE utf8_bin NOT NULL,
  `key` varchar(32) COLLATE utf8_bin NOT NULL,
  `license` varchar(250) COLLATE utf8_bin NOT NULL,
  `install` varchar(250) COLLATE utf8_bin NOT NULL,
  `uninstall` varchar(250) COLLATE utf8_bin NOT NULL,
  `url_developer` varchar(250) COLLATE utf8_bin NOT NULL,
  `settings` text COLLATE utf8_bin NOT NULL,
  `version` varchar(25) COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL,
  `last_update` datetime NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_geo_zone`
--

CREATE TABLE `nts8sd4fd_geo_zone` (
  `geo_zone_id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_language`
--

CREATE TABLE `nts8sd4fd_language` (
  `language_id` int(11) NOT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `code` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `locale` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `image` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `directory` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `filename` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_length_class`
--

CREATE TABLE `nts8sd4fd_length_class` (
  `length_class_id` int(11) NOT NULL,
  `value` decimal(15,8) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_manufacturer`
--

CREATE TABLE `nts8sd4fd_manufacturer` (
  `manufacturer_id` int(11) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `viewed` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_menu`
--

CREATE TABLE `nts8sd4fd_menu` (
  `menu_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `route` varchar(150) NOT NULL,
  `status` int(1) NOT NULL,
  `default` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_menu_link`
--

CREATE TABLE `nts8sd4fd_menu_link` (
  `menu_link_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `link` varchar(250) NOT NULL,
  `tag` varchar(250) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_newsletter`
--

CREATE TABLE `nts8sd4fd_newsletter` (
  `newsletter_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `textbody` text NOT NULL,
  `htmlbody` text NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_notification`
--

CREATE TABLE `nts8sd4fd_notification` (
  `notification_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `message` varchar(250) DEFAULT NULL COMMENT 'Mensaje que sera traducido',
  `url` varchar(250) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_object`
--

CREATE TABLE `nts8sd4fd_object` (
  `object_id` int(11) NOT NULL,
  `object_type` varchar(100) NOT NULL COMMENT 'type of object, for example; product, post, survey, etc.',
  `parent_id` int(11) NOT NULL COMMENT 'if necessary, can make a tree with categories and subcategories',
  `parent_type` varchar(100) NOT NULL COMMENT 'type of the parent, for example; product category, post category, customer group',
  `status_id` int(11) NOT NULL COMMENT 'relation with status table',
  `subtype` varchar(100) NOT NULL COMMENT 'type of this object, for example; assuming this is a product, could be a digital product, normal product, auction product, etc.',
  `status` enum('-1','0','1','') NOT NULL COMMENT '-1:deleted, 0:disactivated, 1:activated',
  `params` text COMMENT 'anything, arrays, classes, objects, strings',
  `sort_order` int(11) NOT NULL COMMENT 'listing order',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_object_to_category`
--

CREATE TABLE `nts8sd4fd_object_to_category` (
  `object_to_category_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `params` text CHARACTER SET utf8 COMMENT 'anything, arrays, classes, objects',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_object_to_store`
--

CREATE TABLE `nts8sd4fd_object_to_store` (
  `object_to_store_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `params` text CHARACTER SET utf8 COMMENT 'anything, arrays, classes, objects',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order`
--

CREATE TABLE `nts8sd4fd_order` (
  `order_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `invoice_prefix` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `store_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `store_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `customer_group_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `telephone` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `rif` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(96) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_company` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_address_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_address_2` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_city` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_zone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_zone_id` int(11) NOT NULL,
  `shipping_country` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_country_id` int(11) NOT NULL,
  `shipping_address_format` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `shipping_method` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_firstname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_lastname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_company` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_address_1` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_address_2` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_city` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_postcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_zone` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_zone_id` int(11) NOT NULL,
  `payment_country` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_country_id` int(11) NOT NULL,
  `payment_address_format` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `payment_method` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `order_status_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` decimal(15,8) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_download`
--

CREATE TABLE `nts8sd4fd_order_download` (
  `order_download_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `filename` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `mask` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `remaining` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_history`
--

CREATE TABLE `nts8sd4fd_order_history` (
  `order_history_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_status_id` int(5) NOT NULL,
  `notify` int(1) NOT NULL DEFAULT '0',
  `comment` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_option`
--

CREATE TABLE `nts8sd4fd_order_option` (
  `order_option_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `prefix` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_payment`
--

CREATE TABLE `nts8sd4fd_order_payment` (
  `order_payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `order_payment_status_id` int(11) NOT NULL,
  `transac_number` varchar(250) NOT NULL,
  `transac_date` date NOT NULL,
  `bank_from` varchar(250) NOT NULL,
  `payment_method` varchar(250) NOT NULL,
  `amount` decimal(11,0) NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_product`
--

CREATE TABLE `nts8sd4fd_order_product` (
  `order_product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `model` varchar(24) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `quantity` int(4) NOT NULL DEFAULT '0',
  `subtract` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_order_total`
--

CREATE TABLE `nts8sd4fd_order_total` (
  `order_total_id` int(10) NOT NULL,
  `order_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `sort_order` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_post`
--

CREATE TABLE `nts8sd4fd_post` (
  `post_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `post_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `sort_order` int(11) NOT NULL,
  `image` varchar(250) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `status` int(1) NOT NULL,
  `date_publish_start` datetime NOT NULL,
  `date_publish_end` datetime NOT NULL,
  `publish` int(1) NOT NULL,
  `allow_reviews` int(1) NOT NULL,
  `template` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product`
--

CREATE TABLE `nts8sd4fd_product` (
  `product_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `model` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `product_type` varchar(255) NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `location` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `stock_status_id` int(11) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `shipping` int(1) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `tax_class_id` int(11) NOT NULL,
  `date_available` date NOT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `weight_class_id` int(11) NOT NULL DEFAULT '0',
  `length` decimal(5,2) NOT NULL DEFAULT '0.00',
  `width` decimal(5,2) NOT NULL DEFAULT '0.00',
  `height` decimal(5,2) NOT NULL DEFAULT '0.00',
  `length_class_id` int(11) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `viewed` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `subtract` int(1) NOT NULL DEFAULT '1',
  `minimum` int(11) NOT NULL DEFAULT '1',
  `cost` decimal(15,4) NOT NULL DEFAULT '0.0000'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_attribute`
--

CREATE TABLE `nts8sd4fd_product_attribute` (
  `product_attribute_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_attribute_group_id` int(11) NOT NULL,
  `group` varchar(50) NOT NULL,
  `label` varchar(250) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `pattern` varchar(250) NOT NULL,
  `default` varchar(250) NOT NULL,
  `required` int(1) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_attribute_group`
--

CREATE TABLE `nts8sd4fd_product_attribute_group` (
  `product_attribute_group_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_discount`
--

CREATE TABLE `nts8sd4fd_product_discount` (
  `product_discount_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_image`
--

CREATE TABLE `nts8sd4fd_product_image` (
  `product_image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_option`
--

CREATE TABLE `nts8sd4fd_product_option` (
  `product_option_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_option_description`
--

CREATE TABLE `nts8sd4fd_product_option_description` (
  `product_option_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_option_value`
--

CREATE TABLE `nts8sd4fd_product_option_value` (
  `product_option_value_id` int(11) NOT NULL,
  `product_option_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(4) NOT NULL DEFAULT '0',
  `subtract` int(1) NOT NULL DEFAULT '0',
  `price` decimal(15,4) NOT NULL,
  `prefix` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_option_value_description`
--

CREATE TABLE `nts8sd4fd_product_option_value_description` (
  `product_option_value_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_related`
--

CREATE TABLE `nts8sd4fd_product_related` (
  `product_id` int(11) NOT NULL,
  `related_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_special`
--

CREATE TABLE `nts8sd4fd_product_special` (
  `product_special_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_tags`
--

CREATE TABLE `nts8sd4fd_product_tags` (
  `product_id` int(11) NOT NULL,
  `tag` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_to_category`
--

CREATE TABLE `nts8sd4fd_product_to_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_to_download`
--

CREATE TABLE `nts8sd4fd_product_to_download` (
  `product_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_to_zone`
--

CREATE TABLE `nts8sd4fd_product_to_zone` (
  `product_to_zone_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `product_id` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_product_type`
--

CREATE TABLE `nts8sd4fd_product_type` (
  `product_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_property`
--

CREATE TABLE `nts8sd4fd_property` (
  `property_id` int(11) NOT NULL,
  `store_id` int(11) DEFAULT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(100) CHARACTER SET utf8 NOT NULL,
  `group` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'group of key pairs',
  `key` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'name of the key',
  `value` text CHARACTER SET utf8 NOT NULL COMMENT 'value of the key',
  `order` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_review`
--

CREATE TABLE `nts8sd4fd_review` (
  `review_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `author` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `rating` int(1) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_review_likes`
--

CREATE TABLE `nts8sd4fd_review_likes` (
  `review_likes_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `object_type` varchar(50) NOT NULL,
  `like` int(1) NOT NULL DEFAULT '0',
  `dislike` int(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_search`
--

CREATE TABLE `nts8sd4fd_search` (
  `search_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `customer_id` text NOT NULL,
  `urlQuery` text NOT NULL,
  `browser` varchar(250) NOT NULL,
  `browser_version` varchar(250) NOT NULL,
  `os` varchar(250) NOT NULL,
  `server` text NOT NULL,
  `session` text NOT NULL,
  `request` text NOT NULL,
  `ip` varchar(20) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_setting`
--

CREATE TABLE `nts8sd4fd_setting` (
  `setting_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `group` varchar(32) COLLATE utf8_bin NOT NULL,
  `key` varchar(64) COLLATE utf8_bin NOT NULL,
  `value` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_stat`
--

CREATE TABLE `nts8sd4fd_stat` (
  `stat_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(250) CHARACTER SET utf8 NOT NULL,
  `store_id` int(11) NOT NULL,
  `store_url` varchar(250) CHARACTER SET utf8 NOT NULL,
  `server` text CHARACTER SET utf8 NOT NULL COMMENT 'server server var',
  `session` text CHARACTER SET utf8 NOT NULL COMMENT 'server session var',
  `request` text CHARACTER SET utf8 NOT NULL COMMENT 'server request var',
  `ref` varchar(250) CHARACTER SET utf8 NOT NULL COMMENT 'browser previous url',
  `email` varchar(255) NOT NULL,
  `browser` varchar(150) CHARACTER SET utf8 NOT NULL,
  `browser_version` varchar(50) CHARACTER SET utf8 NOT NULL,
  `os` varchar(150) CHARACTER SET utf8 NOT NULL,
  `ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_status`
--

CREATE TABLE `nts8sd4fd_status` (
  `status_id` int(11) NOT NULL,
  `object_type` varchar(100) NOT NULL COMMENT 'kind of object, for examples; kind of product, kind of post, etc.',
  `ref` varchar(255) DEFAULT NULL COMMENT 'anything, arrays, objects',
  `status` varchar(255) NOT NULL DEFAULT '1' COMMENT '-1:deleted, 0:disactivated, 1:activated',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_store`
--

CREATE TABLE `nts8sd4fd_store` (
  `store_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `folder` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_task`
--

CREATE TABLE `nts8sd4fd_task` (
  `task_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `task` varchar(250) NOT NULL,
  `type` varchar(50) NOT NULL,
  `params` text NOT NULL,
  `time_interval` varchar(50) NOT NULL,
  `time_exec` datetime NOT NULL,
  `time_last_exec` datetime NOT NULL,
  `run_once` int(1) NOT NULL,
  `status` int(2) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `data_added` datetime NOT NULL,
  `date_start_exec` datetime NOT NULL,
  `date_end_exec` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_task_exec`
--

CREATE TABLE `nts8sd4fd_task_exec` (
  `task_exec_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_task_queue`
--

CREATE TABLE `nts8sd4fd_task_queue` (
  `task_queue_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `params` text NOT NULL,
  `time_exec` datetime NOT NULL,
  `status` int(2) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_tax_class`
--

CREATE TABLE `nts8sd4fd_tax_class` (
  `tax_class_id` int(11) NOT NULL,
  `title` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_tax_rate`
--

CREATE TABLE `nts8sd4fd_tax_rate` (
  `tax_rate_id` int(11) NOT NULL,
  `geo_zone_id` int(11) NOT NULL DEFAULT '0',
  `tax_class_id` int(11) NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  `rate` decimal(7,4) NOT NULL DEFAULT '0.0000',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_template`
--

CREATE TABLE `nts8sd4fd_template` (
  `template_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `version` varchar(20) NOT NULL,
  `for_nt_version` varchar(20) NOT NULL,
  `colors` varchar(250) NOT NULL,
  `cols` int(1) NOT NULL,
  `scheme` varchar(250) NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_theme`
--

CREATE TABLE `nts8sd4fd_theme` (
  `theme_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `template` varchar(150) NOT NULL DEFAULT 'default',
  `default` int(1) NOT NULL,
  `status` int(1) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `date_publish_start` datetime NOT NULL,
  `date_publish_end` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_theme_style`
--

CREATE TABLE `nts8sd4fd_theme_style` (
  `theme_style_id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL,
  `selector` varchar(250) NOT NULL,
  `property` varchar(250) NOT NULL,
  `value` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_url_alias`
--

CREATE TABLE `nts8sd4fd_url_alias` (
  `url_alias_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `object_type` varchar(50) COLLATE utf8_bin NOT NULL,
  `query` varchar(255) COLLATE utf8_bin NOT NULL,
  `keyword` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_user`
--

CREATE TABLE `nts8sd4fd_user` (
  `user_id` int(11) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  `username` varchar(20) COLLATE utf8_bin NOT NULL,
  `password` varchar(100) COLLATE utf8_bin NOT NULL,
  `firstname` varchar(32) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(32) COLLATE utf8_bin NOT NULL,
  `email` varchar(96) COLLATE utf8_bin NOT NULL,
  `image` varchar(255) COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL,
  `ip` varchar(15) COLLATE utf8_bin NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_user_activity`
--

CREATE TABLE `nts8sd4fd_user_activity` (
  `user_activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `event` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip` varchar(20) NOT NULL,
  `browser` varchar(50) NOT NULL,
  `browser_version` varchar(20) NOT NULL,
  `os` varchar(50) NOT NULL,
  `session` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_user_group`
--

CREATE TABLE `nts8sd4fd_user_group` (
  `user_group_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `permission` text COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_warehouse_movement`
--

CREATE TABLE `nts8sd4fd_warehouse_movement` (
  `movement_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `shelf_id` int(11) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `weight` decimal(11,0) NOT NULL,
  `qty` decimal(11,0) NOT NULL,
  `movement` varchar(50) NOT NULL,
  `rotation` varchar(50) NOT NULL,
  `barcode_number` varchar(50) NOT NULL,
  `barcode_type` varchar(50) NOT NULL,
  `date_leave` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_weight_class`
--

CREATE TABLE `nts8sd4fd_weight_class` (
  `weight_class_id` int(11) NOT NULL,
  `value` decimal(15,8) NOT NULL DEFAULT '0.00000000'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_widget`
--

CREATE TABLE `nts8sd4fd_widget` (
  `widget_id` bigint(20) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(250) NOT NULL,
  `name` varchar(250) NOT NULL,
  `extension` varchar(50) NOT NULL,
  `position` varchar(50) NOT NULL,
  `app` varchar(50) NOT NULL,
  `order` int(2) NOT NULL,
  `settings` text NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_widget_landing_page`
--

CREATE TABLE `nts8sd4fd_widget_landing_page` (
  `widget_landing_page_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `landing_page` varchar(150) NOT NULL,
  `object_type` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_zone`
--

CREATE TABLE `nts8sd4fd_zone` (
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `code` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nts8sd4fd_zone_to_geo_zone`
--

CREATE TABLE `nts8sd4fd_zone_to_geo_zone` (
  `zone_to_geo_zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL DEFAULT '0',
  `geo_zone_id` int(11) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nts8sd4fd_address`
--
ALTER TABLE `nts8sd4fd_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `nts8sd4fd_balance`
--
ALTER TABLE `nts8sd4fd_balance`
  ADD PRIMARY KEY (`balance_id`);

--
-- Indexes for table `nts8sd4fd_bank`
--
ALTER TABLE `nts8sd4fd_bank`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `nts8sd4fd_bank_account`
--
ALTER TABLE `nts8sd4fd_bank_account`
  ADD PRIMARY KEY (`bank_account_id`),
  ADD UNIQUE KEY `number` (`number`);

--
-- Indexes for table `nts8sd4fd_banner`
--
ALTER TABLE `nts8sd4fd_banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `nts8sd4fd_banner_item`
--
ALTER TABLE `nts8sd4fd_banner_item`
  ADD PRIMARY KEY (`banner_item_id`);

--
-- Indexes for table `nts8sd4fd_campaign`
--
ALTER TABLE `nts8sd4fd_campaign`
  ADD PRIMARY KEY (`campaign_id`);

--
-- Indexes for table `nts8sd4fd_campaign_contact`
--
ALTER TABLE `nts8sd4fd_campaign_contact`
  ADD PRIMARY KEY (`campaign_contact_id`);

--
-- Indexes for table `nts8sd4fd_campaign_link`
--
ALTER TABLE `nts8sd4fd_campaign_link`
  ADD PRIMARY KEY (`campaign_link_id`);

--
-- Indexes for table `nts8sd4fd_campaign_link_stat`
--
ALTER TABLE `nts8sd4fd_campaign_link_stat`
  ADD PRIMARY KEY (`campaign_link_stat_id`);

--
-- Indexes for table `nts8sd4fd_campaign_stat`
--
ALTER TABLE `nts8sd4fd_campaign_stat`
  ADD PRIMARY KEY (`campaign_stat_id`);

--
-- Indexes for table `nts8sd4fd_category`
--
ALTER TABLE `nts8sd4fd_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `nts8sd4fd_contact`
--
ALTER TABLE `nts8sd4fd_contact`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `nts8sd4fd_contact_list`
--
ALTER TABLE `nts8sd4fd_contact_list`
  ADD PRIMARY KEY (`contact_list_id`);

--
-- Indexes for table `nts8sd4fd_contact_to_list`
--
ALTER TABLE `nts8sd4fd_contact_to_list`
  ADD PRIMARY KEY (`contact_to_list_id`),
  ADD UNIQUE KEY `contact_list` (`contact_list_id`,`contact_id`);

--
-- Indexes for table `nts8sd4fd_country`
--
ALTER TABLE `nts8sd4fd_country`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `nts8sd4fd_coupon`
--
ALTER TABLE `nts8sd4fd_coupon`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `nts8sd4fd_coupon_category`
--
ALTER TABLE `nts8sd4fd_coupon_category`
  ADD PRIMARY KEY (`coupon_id`,`category_id`);

--
-- Indexes for table `nts8sd4fd_coupon_history`
--
ALTER TABLE `nts8sd4fd_coupon_history`
  ADD PRIMARY KEY (`coupon_history_id`);

--
-- Indexes for table `nts8sd4fd_coupon_product`
--
ALTER TABLE `nts8sd4fd_coupon_product`
  ADD PRIMARY KEY (`coupon_product_id`);

--
-- Indexes for table `nts8sd4fd_currency`
--
ALTER TABLE `nts8sd4fd_currency`
  ADD PRIMARY KEY (`currency_id`);

--
-- Indexes for table `nts8sd4fd_customer`
--
ALTER TABLE `nts8sd4fd_customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `nts8sd4fd_customer_group`
--
ALTER TABLE `nts8sd4fd_customer_group`
  ADD PRIMARY KEY (`customer_group_id`);

--
-- Indexes for table `nts8sd4fd_description`
--
ALTER TABLE `nts8sd4fd_description`
  ADD PRIMARY KEY (`description_id`),
  ADD UNIQUE KEY `object_id` (`object_id`,`object_type`,`language_id`);

--
-- Indexes for table `nts8sd4fd_download`
--
ALTER TABLE `nts8sd4fd_download`
  ADD PRIMARY KEY (`download_id`);

--
-- Indexes for table `nts8sd4fd_extension`
--
ALTER TABLE `nts8sd4fd_extension`
  ADD PRIMARY KEY (`extension_id`);

--
-- Indexes for table `nts8sd4fd_geo_zone`
--
ALTER TABLE `nts8sd4fd_geo_zone`
  ADD PRIMARY KEY (`geo_zone_id`);

--
-- Indexes for table `nts8sd4fd_language`
--
ALTER TABLE `nts8sd4fd_language`
  ADD PRIMARY KEY (`language_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `nts8sd4fd_length_class`
--
ALTER TABLE `nts8sd4fd_length_class`
  ADD PRIMARY KEY (`length_class_id`);

--
-- Indexes for table `nts8sd4fd_manufacturer`
--
ALTER TABLE `nts8sd4fd_manufacturer`
  ADD PRIMARY KEY (`manufacturer_id`);

--
-- Indexes for table `nts8sd4fd_menu`
--
ALTER TABLE `nts8sd4fd_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `nts8sd4fd_menu_link`
--
ALTER TABLE `nts8sd4fd_menu_link`
  ADD PRIMARY KEY (`menu_link_id`);

--
-- Indexes for table `nts8sd4fd_newsletter`
--
ALTER TABLE `nts8sd4fd_newsletter`
  ADD PRIMARY KEY (`newsletter_id`);

--
-- Indexes for table `nts8sd4fd_notification`
--
ALTER TABLE `nts8sd4fd_notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `nts8sd4fd_object`
--
ALTER TABLE `nts8sd4fd_object`
  ADD PRIMARY KEY (`object_id`);

--
-- Indexes for table `nts8sd4fd_object_to_category`
--
ALTER TABLE `nts8sd4fd_object_to_category`
  ADD PRIMARY KEY (`object_to_category_id`),
  ADD UNIQUE KEY `object_to_category` (`object_id`,`object_type`,`category_id`);

--
-- Indexes for table `nts8sd4fd_object_to_store`
--
ALTER TABLE `nts8sd4fd_object_to_store`
  ADD PRIMARY KEY (`object_to_store_id`),
  ADD UNIQUE KEY `object_to_store` (`object_id`,`object_type`,`store_id`);

--
-- Indexes for table `nts8sd4fd_order`
--
ALTER TABLE `nts8sd4fd_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `nts8sd4fd_order_download`
--
ALTER TABLE `nts8sd4fd_order_download`
  ADD PRIMARY KEY (`order_download_id`);

--
-- Indexes for table `nts8sd4fd_order_history`
--
ALTER TABLE `nts8sd4fd_order_history`
  ADD PRIMARY KEY (`order_history_id`);

--
-- Indexes for table `nts8sd4fd_order_option`
--
ALTER TABLE `nts8sd4fd_order_option`
  ADD PRIMARY KEY (`order_option_id`);

--
-- Indexes for table `nts8sd4fd_order_payment`
--
ALTER TABLE `nts8sd4fd_order_payment`
  ADD PRIMARY KEY (`order_payment_id`);

--
-- Indexes for table `nts8sd4fd_order_product`
--
ALTER TABLE `nts8sd4fd_order_product`
  ADD PRIMARY KEY (`order_product_id`);

--
-- Indexes for table `nts8sd4fd_order_total`
--
ALTER TABLE `nts8sd4fd_order_total`
  ADD PRIMARY KEY (`order_total_id`),
  ADD KEY `idx_orders_total_orders_id` (`order_id`);

--
-- Indexes for table `nts8sd4fd_post`
--
ALTER TABLE `nts8sd4fd_post`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `nts8sd4fd_product`
--
ALTER TABLE `nts8sd4fd_product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `model` (`model`);

--
-- Indexes for table `nts8sd4fd_product_attribute`
--
ALTER TABLE `nts8sd4fd_product_attribute`
  ADD PRIMARY KEY (`product_attribute_id`);

--
-- Indexes for table `nts8sd4fd_product_attribute_group`
--
ALTER TABLE `nts8sd4fd_product_attribute_group`
  ADD PRIMARY KEY (`product_attribute_group_id`);

--
-- Indexes for table `nts8sd4fd_product_discount`
--
ALTER TABLE `nts8sd4fd_product_discount`
  ADD PRIMARY KEY (`product_discount_id`);

--
-- Indexes for table `nts8sd4fd_product_image`
--
ALTER TABLE `nts8sd4fd_product_image`
  ADD PRIMARY KEY (`product_image_id`);

--
-- Indexes for table `nts8sd4fd_product_option`
--
ALTER TABLE `nts8sd4fd_product_option`
  ADD PRIMARY KEY (`product_option_id`);

--
-- Indexes for table `nts8sd4fd_product_option_description`
--
ALTER TABLE `nts8sd4fd_product_option_description`
  ADD PRIMARY KEY (`product_option_id`,`language_id`);

--
-- Indexes for table `nts8sd4fd_product_option_value`
--
ALTER TABLE `nts8sd4fd_product_option_value`
  ADD PRIMARY KEY (`product_option_value_id`);

--
-- Indexes for table `nts8sd4fd_product_option_value_description`
--
ALTER TABLE `nts8sd4fd_product_option_value_description`
  ADD PRIMARY KEY (`product_option_value_id`,`language_id`);

--
-- Indexes for table `nts8sd4fd_product_related`
--
ALTER TABLE `nts8sd4fd_product_related`
  ADD PRIMARY KEY (`product_id`,`related_id`);

--
-- Indexes for table `nts8sd4fd_product_special`
--
ALTER TABLE `nts8sd4fd_product_special`
  ADD PRIMARY KEY (`product_special_id`);

--
-- Indexes for table `nts8sd4fd_product_tags`
--
ALTER TABLE `nts8sd4fd_product_tags`
  ADD PRIMARY KEY (`product_id`,`tag`,`language_id`);

--
-- Indexes for table `nts8sd4fd_product_to_category`
--
ALTER TABLE `nts8sd4fd_product_to_category`
  ADD PRIMARY KEY (`product_id`,`category_id`);

--
-- Indexes for table `nts8sd4fd_product_to_download`
--
ALTER TABLE `nts8sd4fd_product_to_download`
  ADD PRIMARY KEY (`product_id`,`download_id`);

--
-- Indexes for table `nts8sd4fd_product_to_zone`
--
ALTER TABLE `nts8sd4fd_product_to_zone`
  ADD PRIMARY KEY (`product_to_zone_id`);

--
-- Indexes for table `nts8sd4fd_product_type`
--
ALTER TABLE `nts8sd4fd_product_type`
  ADD PRIMARY KEY (`product_type_id`);

--
-- Indexes for table `nts8sd4fd_property`
--
ALTER TABLE `nts8sd4fd_property`
  ADD PRIMARY KEY (`property_id`),
  ADD UNIQUE KEY `property` (`object_id`,`object_type`,`group`,`key`);

--
-- Indexes for table `nts8sd4fd_review`
--
ALTER TABLE `nts8sd4fd_review`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `nts8sd4fd_review_likes`
--
ALTER TABLE `nts8sd4fd_review_likes`
  ADD PRIMARY KEY (`review_likes_id`),
  ADD UNIQUE KEY `review_like` (`review_id`,`customer_id`);

--
-- Indexes for table `nts8sd4fd_search`
--
ALTER TABLE `nts8sd4fd_search`
  ADD PRIMARY KEY (`search_id`);

--
-- Indexes for table `nts8sd4fd_setting`
--
ALTER TABLE `nts8sd4fd_setting`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `nts8sd4fd_stat`
--
ALTER TABLE `nts8sd4fd_stat`
  ADD PRIMARY KEY (`stat_id`);

--
-- Indexes for table `nts8sd4fd_status`
--
ALTER TABLE `nts8sd4fd_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `nts8sd4fd_store`
--
ALTER TABLE `nts8sd4fd_store`
  ADD PRIMARY KEY (`store_id`),
  ADD UNIQUE KEY `folder` (`folder`);

--
-- Indexes for table `nts8sd4fd_task`
--
ALTER TABLE `nts8sd4fd_task`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `nts8sd4fd_task_exec`
--
ALTER TABLE `nts8sd4fd_task_exec`
  ADD PRIMARY KEY (`task_exec_id`);

--
-- Indexes for table `nts8sd4fd_task_queue`
--
ALTER TABLE `nts8sd4fd_task_queue`
  ADD PRIMARY KEY (`task_queue_id`);

--
-- Indexes for table `nts8sd4fd_tax_class`
--
ALTER TABLE `nts8sd4fd_tax_class`
  ADD PRIMARY KEY (`tax_class_id`);

--
-- Indexes for table `nts8sd4fd_tax_rate`
--
ALTER TABLE `nts8sd4fd_tax_rate`
  ADD PRIMARY KEY (`tax_rate_id`);

--
-- Indexes for table `nts8sd4fd_template`
--
ALTER TABLE `nts8sd4fd_template`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `nts8sd4fd_theme`
--
ALTER TABLE `nts8sd4fd_theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `nts8sd4fd_theme_style`
--
ALTER TABLE `nts8sd4fd_theme_style`
  ADD PRIMARY KEY (`theme_style_id`);

--
-- Indexes for table `nts8sd4fd_url_alias`
--
ALTER TABLE `nts8sd4fd_url_alias`
  ADD PRIMARY KEY (`url_alias_id`),
  ADD UNIQUE KEY `keyword` (`keyword`,`language_id`),
  ADD UNIQUE KEY `slug` (`object_id`,`language_id`,`object_type`);

--
-- Indexes for table `nts8sd4fd_user`
--
ALTER TABLE `nts8sd4fd_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `nts8sd4fd_user_activity`
--
ALTER TABLE `nts8sd4fd_user_activity`
  ADD PRIMARY KEY (`user_activity_id`);

--
-- Indexes for table `nts8sd4fd_user_group`
--
ALTER TABLE `nts8sd4fd_user_group`
  ADD PRIMARY KEY (`user_group_id`);

--
-- Indexes for table `nts8sd4fd_warehouse_movement`
--
ALTER TABLE `nts8sd4fd_warehouse_movement`
  ADD PRIMARY KEY (`movement_id`);

--
-- Indexes for table `nts8sd4fd_weight_class`
--
ALTER TABLE `nts8sd4fd_weight_class`
  ADD PRIMARY KEY (`weight_class_id`);

--
-- Indexes for table `nts8sd4fd_widget`
--
ALTER TABLE `nts8sd4fd_widget`
  ADD PRIMARY KEY (`widget_id`);

--
-- Indexes for table `nts8sd4fd_widget_landing_page`
--
ALTER TABLE `nts8sd4fd_widget_landing_page`
  ADD PRIMARY KEY (`widget_landing_page_id`);

--
-- Indexes for table `nts8sd4fd_zone`
--
ALTER TABLE `nts8sd4fd_zone`
  ADD PRIMARY KEY (`zone_id`);

--
-- Indexes for table `nts8sd4fd_zone_to_geo_zone`
--
ALTER TABLE `nts8sd4fd_zone_to_geo_zone`
  ADD PRIMARY KEY (`zone_to_geo_zone_id`),
  ADD UNIQUE KEY `zone_geo_zone` (`zone_id`,`country_id`,`geo_zone_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nts8sd4fd_address`
--
ALTER TABLE `nts8sd4fd_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_balance`
--
ALTER TABLE `nts8sd4fd_balance`
  MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_bank`
--
ALTER TABLE `nts8sd4fd_bank`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_bank_account`
--
ALTER TABLE `nts8sd4fd_bank_account`
  MODIFY `bank_account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_banner`
--
ALTER TABLE `nts8sd4fd_banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_banner_item`
--
ALTER TABLE `nts8sd4fd_banner_item`
  MODIFY `banner_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_campaign`
--
ALTER TABLE `nts8sd4fd_campaign`
  MODIFY `campaign_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_campaign_contact`
--
ALTER TABLE `nts8sd4fd_campaign_contact`
  MODIFY `campaign_contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_campaign_link`
--
ALTER TABLE `nts8sd4fd_campaign_link`
  MODIFY `campaign_link_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_campaign_link_stat`
--
ALTER TABLE `nts8sd4fd_campaign_link_stat`
  MODIFY `campaign_link_stat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_campaign_stat`
--
ALTER TABLE `nts8sd4fd_campaign_stat`
  MODIFY `campaign_stat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_category`
--
ALTER TABLE `nts8sd4fd_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_contact`
--
ALTER TABLE `nts8sd4fd_contact`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_contact_list`
--
ALTER TABLE `nts8sd4fd_contact_list`
  MODIFY `contact_list_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_contact_to_list`
--
ALTER TABLE `nts8sd4fd_contact_to_list`
  MODIFY `contact_to_list_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_country`
--
ALTER TABLE `nts8sd4fd_country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_coupon`
--
ALTER TABLE `nts8sd4fd_coupon`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_coupon_history`
--
ALTER TABLE `nts8sd4fd_coupon_history`
  MODIFY `coupon_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_coupon_product`
--
ALTER TABLE `nts8sd4fd_coupon_product`
  MODIFY `coupon_product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_currency`
--
ALTER TABLE `nts8sd4fd_currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_customer`
--
ALTER TABLE `nts8sd4fd_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_customer_group`
--
ALTER TABLE `nts8sd4fd_customer_group`
  MODIFY `customer_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_description`
--
ALTER TABLE `nts8sd4fd_description`
  MODIFY `description_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_download`
--
ALTER TABLE `nts8sd4fd_download`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_extension`
--
ALTER TABLE `nts8sd4fd_extension`
  MODIFY `extension_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_geo_zone`
--
ALTER TABLE `nts8sd4fd_geo_zone`
  MODIFY `geo_zone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_language`
--
ALTER TABLE `nts8sd4fd_language`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_length_class`
--
ALTER TABLE `nts8sd4fd_length_class`
  MODIFY `length_class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_manufacturer`
--
ALTER TABLE `nts8sd4fd_manufacturer`
  MODIFY `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_menu`
--
ALTER TABLE `nts8sd4fd_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_menu_link`
--
ALTER TABLE `nts8sd4fd_menu_link`
  MODIFY `menu_link_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_newsletter`
--
ALTER TABLE `nts8sd4fd_newsletter`
  MODIFY `newsletter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_notification`
--
ALTER TABLE `nts8sd4fd_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_object`
--
ALTER TABLE `nts8sd4fd_object`
  MODIFY `object_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_object_to_category`
--
ALTER TABLE `nts8sd4fd_object_to_category`
  MODIFY `object_to_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_object_to_store`
--
ALTER TABLE `nts8sd4fd_object_to_store`
  MODIFY `object_to_store_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order`
--
ALTER TABLE `nts8sd4fd_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_download`
--
ALTER TABLE `nts8sd4fd_order_download`
  MODIFY `order_download_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_history`
--
ALTER TABLE `nts8sd4fd_order_history`
  MODIFY `order_history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_option`
--
ALTER TABLE `nts8sd4fd_order_option`
  MODIFY `order_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_payment`
--
ALTER TABLE `nts8sd4fd_order_payment`
  MODIFY `order_payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_product`
--
ALTER TABLE `nts8sd4fd_order_product`
  MODIFY `order_product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_order_total`
--
ALTER TABLE `nts8sd4fd_order_total`
  MODIFY `order_total_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_post`
--
ALTER TABLE `nts8sd4fd_post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product`
--
ALTER TABLE `nts8sd4fd_product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_attribute`
--
ALTER TABLE `nts8sd4fd_product_attribute`
  MODIFY `product_attribute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_attribute_group`
--
ALTER TABLE `nts8sd4fd_product_attribute_group`
  MODIFY `product_attribute_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_discount`
--
ALTER TABLE `nts8sd4fd_product_discount`
  MODIFY `product_discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_image`
--
ALTER TABLE `nts8sd4fd_product_image`
  MODIFY `product_image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_option`
--
ALTER TABLE `nts8sd4fd_product_option`
  MODIFY `product_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_option_value`
--
ALTER TABLE `nts8sd4fd_product_option_value`
  MODIFY `product_option_value_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_special`
--
ALTER TABLE `nts8sd4fd_product_special`
  MODIFY `product_special_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_to_zone`
--
ALTER TABLE `nts8sd4fd_product_to_zone`
  MODIFY `product_to_zone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_product_type`
--
ALTER TABLE `nts8sd4fd_product_type`
  MODIFY `product_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_property`
--
ALTER TABLE `nts8sd4fd_property`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_review`
--
ALTER TABLE `nts8sd4fd_review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_review_likes`
--
ALTER TABLE `nts8sd4fd_review_likes`
  MODIFY `review_likes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_search`
--
ALTER TABLE `nts8sd4fd_search`
  MODIFY `search_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_setting`
--
ALTER TABLE `nts8sd4fd_setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_stat`
--
ALTER TABLE `nts8sd4fd_stat`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_status`
--
ALTER TABLE `nts8sd4fd_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_store`
--
ALTER TABLE `nts8sd4fd_store`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_task`
--
ALTER TABLE `nts8sd4fd_task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_task_exec`
--
ALTER TABLE `nts8sd4fd_task_exec`
  MODIFY `task_exec_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_task_queue`
--
ALTER TABLE `nts8sd4fd_task_queue`
  MODIFY `task_queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_tax_class`
--
ALTER TABLE `nts8sd4fd_tax_class`
  MODIFY `tax_class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_tax_rate`
--
ALTER TABLE `nts8sd4fd_tax_rate`
  MODIFY `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_template`
--
ALTER TABLE `nts8sd4fd_template`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_theme`
--
ALTER TABLE `nts8sd4fd_theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_theme_style`
--
ALTER TABLE `nts8sd4fd_theme_style`
  MODIFY `theme_style_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_url_alias`
--
ALTER TABLE `nts8sd4fd_url_alias`
  MODIFY `url_alias_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_user`
--
ALTER TABLE `nts8sd4fd_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_user_activity`
--
ALTER TABLE `nts8sd4fd_user_activity`
  MODIFY `user_activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_user_group`
--
ALTER TABLE `nts8sd4fd_user_group`
  MODIFY `user_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_warehouse_movement`
--
ALTER TABLE `nts8sd4fd_warehouse_movement`
  MODIFY `movement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_weight_class`
--
ALTER TABLE `nts8sd4fd_weight_class`
  MODIFY `weight_class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_widget`
--
ALTER TABLE `nts8sd4fd_widget`
  MODIFY `widget_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_widget_landing_page`
--
ALTER TABLE `nts8sd4fd_widget_landing_page`
  MODIFY `widget_landing_page_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_zone`
--
ALTER TABLE `nts8sd4fd_zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nts8sd4fd_zone_to_geo_zone`
--
ALTER TABLE `nts8sd4fd_zone_to_geo_zone`
  MODIFY `zone_to_geo_zone_id` int(11) NOT NULL AUTO_INCREMENT;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
