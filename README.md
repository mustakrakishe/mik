# Mik

A web application for graphical visualization of the technological parameters trends from the continuous-generated file.

## Description

An application allows to seen archived and stream current value of the technological parameters signals.
A data source must be represented with log files of the specialized program product [MIK-Registrator](http://microl.ua/index.php?page=shop.product_details&flypage=garden_flypage.tpl&product_id=107&category_id=26&option=com_virtuemart&Itemid=71) by Microl.

## A work schema
1. MIK-Registrator by Microl collects controller's data.
2. MIK-Registrator logs data into an .arh file.
3. Mik web app requires this .arh file:
    - once in case archives seening;
    - continuously with a specified time interval in case data streaming.
4. Mik web app parses file and represents data wiith graphic.