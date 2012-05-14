<?php
class Guestticket extends Zend_Db_Table {
   protected $_name = 'guestticket';
   
   /*
   Niezbyt skomplikowane – prawda? Na szczęście dla nas, nasze 
   potrzeby są na tyle podstawowe, że wszelką funkcjonalność przez
   nas wymaganą dostarczy nam obiekt Zend_Db_Table sam w sobie. 
   Jeśli jednak pisalibyśmy aplikację, gdzie potrzebowalibyśmy 
   więcej funkcjonalności niż ta podstawowa, ten obiekt byłby 
   właściwym miejscem na ich implementację. Dla przykładu mogły 
   by to być funkcje “wyszukiwania”, które uwzględniając 
   szczególne warunki i filtry, zwracały by kolekcję specjalnie 
   przygotowanych danych.
   */
}