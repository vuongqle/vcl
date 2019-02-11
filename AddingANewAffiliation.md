---
title: Adding a New Affiliation
last_updated: Feb 07, 2019
permalink: adding-a-new-affiliation.html
---

Adding new affiliations is normally done when a VCL deployment is initially installed or when an existing deployment grows to include additional institutions.  To add a new affiliation, you will need to manually add an entry to the VCL database.  See the [Manual Database Manipulation](Manual-Database-Manipulation.html) for more information.

## Add a Row to the affiliation Table

To add a new affiliation to the VCL database, a row must be inserted into the affiliation table.  The affiliation table in a stock VCL database contains two rows:

    SELECT * FROM affiliation;

    +----+--------+----------+----------------+----------------+-------------+----------+---------+
    | id | name   | shibname | dataUpdateText | sitewwwaddress | helpaddress | shibonly | theme   |
    +----+--------+----------+----------------+----------------+-------------+----------+---------+
    |  1 | Local  | NULL     |                | NULL           | NULL        |        0 | default |
    |  2 | Global | NULL     |                | NULL           | NULL        |        0 | default |
    +----+--------+----------+----------------+----------------+-------------+----------+---------+

Add a new row to the ***affiliation*** table by executing an SQL query similar to the following:

    INSERT INTO vcl.affiliation (name, sitewwwaddress, helpaddress) VALUES ('VCL University LDAP', 'www.example.edu', 'help@example.edu');

Verify the row has been added:

    SELECT * FROM affiliation;

    +----+---------------------+----------+----------------+-----------------+------------------+----------+---------+
    | id | name                | shibname | dataUpdateText | sitewwwaddress  | helpaddress      | shibonly | theme   |
    +----+---------------------+----------+----------------+-----------------+------------------+----------+---------+
    |  1 | Local               | NULL     |                | NULL            | NULL             |        0 | default |
    |  2 | Global              | NULL     |                | NULL            | NULL             |        0 | default |
    |  3 | VCL University LDAP | NULL     |                | www.example.edu | help@example.edu |        0 | default |
    +----+---------------------+----------+----------------+-----------------+------------------+----------+---------+
