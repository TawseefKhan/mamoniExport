# MaMoni Survey Data Exporter

## Prerequisites
1) PHP preferably 5.6 <br />
2) GIT

## Description
This script simple calls the existing MaMoni API, fetches the data and creates an SQL dump file which includes 55 Major tables and 1 User table.

## Basic Usage
First clone the project.
`git clone https://gitlab.com/TawseefKhan/mamoni_export.git`<br />
Then, once in the same directory run the following command<br />
`php MaMoniExport [username] [password] [filename]`<br />
Example<br />
`php MaMoniExport user pass data.sql`<br />
Upu must enter the admin user and pass to get all the data.<br />

## Output
The script will provide you with an sql and also a report on how many rows have been inserted for each table. The script will also automatically select the best possible geoCode.<br />
However in some cases the geoCode becomes Ambiguous in which. The report should also mention the no. of rows it failed to automatically recover the geo code. <br />
But such rows will still be inserted with an extra field names `_geoProxy`. This column will contain a json with all the Ambiguous geoCode possibilities for that certain row only. <br />

## Advanced Configuration
You can configure the order in which the columns are created.<br />
1) Enter The bin/resources directory<br />
2) Open any one of the following<br />
    a)dh_antenantals.csv<br />
    b)dh_familyplan.csv<br />
    c)dh_satelliteclinic.csv<br />
    d)dh_inventory.csv<br />
    e)dh_sickchild.csv<br />
3) Rearrange the column names as you please.<br />
4) If you feel that you do not require any of the columns you can just delete that row. But initially all possible fields are already added.<br />

##### Please note: You can only change the sequence of the fields that are in the csv folder. Fields such as `id` or `dev` cant be rearranged. 