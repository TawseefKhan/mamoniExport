# MaMoni Survey Data Exporter

## Prerequisites
1) PHP preferably 5.6
2) GIT

## Description
This script simple calls the existing MaMoni API, fetches the data and creates an SQL dump file which includes 55 Major tables and 1 User table.

## Basic Usage
First clone the project.
`git clone https://gitlab.com/TawseefKhan/mamoni_export.git`
Then, once in the same directory run the following command
`php MaMoniExport [username] [password] [filename]`
Example
`php MaMoniExport user pass data.sql`
Upu must enter the admin user and pass to get all the data.

## Output
The script will provide you with an sql and also a report on how many rows have been inserted for each table. The script will also automatically select the best possible geoCode.
However in some cases the geoCode becomes Ambiguous in which. The report should also mention the no. of rows it failed to automatically recover the geo code. 
But such rows will still be inserted with an extra field names `_geoProxy`. This column will contain a json with all the Ambiguous geoCode possibilities for that certain row only. 

## Advanced Configuration
You can configure the order in which the columns are created.
1) Enter The bin/resources directory
2) Open any one of the following
    a)dh_antenantals.csv
    b)dh_familyplan.csv
    c)dh_satelliteclinic.csv
    d)dh_inventory.csv
    e)dh_sickchild.csv
3) Rearrange the column names as you please.
4) If you feel that you do not require any of the columns you can just delete that row. But initially all possible fields are already added.

##### Please note: You can only change the sequence of the fields that are in the csv folder. Fields such as `id` or `dev` cant be rearranged. 