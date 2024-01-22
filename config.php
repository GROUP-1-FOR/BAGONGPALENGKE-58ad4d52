<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "bagong_palengke_db");

if ($connect === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
