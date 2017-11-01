# Data Warehouse Loader (alpha)

This application is intended to export data from the Sierra Database and load it into AWS Redshift.

This package adheres to [PSR-1](http://www.php-fig.org/psr/psr-1/), 
[PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/) 
(using the [Composer](https://getcomposer.org/) autoloader).

## Requirements

* PHP >=7.0 
  * [pdo_pdgsql](http://php.net/manual/en/ref.pdo-pgsql.php)

Homebrew is highly recommended for PHP:
  * `brew install php71`
  * `brew install php71-pdo-pgsql`

## Installation

1. Clone the repo.
2. Install required dependencies.
   * Run `composer install` to install PHP packages.
3. Setup [configuration](#configuration) files.
   * Copy the `.env.sample` file to `.env`.

## Usage

### Export and Load Data

~~~~
$ ./load [table_name] 
~~~~

`table_name`: Name of the table to export data from and load into Redshift. Currently only `circ_trans` is supported.

To, specify a starting ID for the export process:

~~~~
$ ./load [table_name] [start_id]
~~~~

`start_id`: The starting ID to use for the export process. By default, if no starting ID is specified, exporting will start from the last exported ID.

### Displaying an Export

~~~~
$ ./display [file_name] 
~~~~

`file_name`: Name of the export file to display. By default, files are saved in the `exports/` directory.
