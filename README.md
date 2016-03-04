KrISS MVVM
==========
A simple and smart (or stupid) MVVM framework

WIPOC project (Work In Progress and Proof Of Concept)

Presentation
------------
The goal is to build a really flexible MVVM framework that let you
build very rapidly a skeleton for apps. Look at the mini.php file and
you will see how it's easy to have a very simple rest app for Test
class.

To illustrate the flexibility, KrISS MVVM already works with different projects :
* [Dice](https://github.com/level-2/dice) for the container
* [Transphporm](https://github.com/level-2/maphper) for the template
* [Maphper](https://github.com/level-2/maphper) for the model
* [Validation]() for the validation

Installation
------------
```bash
cd /var/www/html #it's up to you
git clone https://github.com/kriss/mvvm
```
you can go to your mvvm directory with a browser and the demo should work.

http://localhost/mvvm/demo.php

```bash
cd mvvm
composer update #if composer is globally installed
mkdir data
chmod 777 data #well, that's just for a try
```
you can now view a real example

http://localhost/mvvm/index.php

Todo
----
A lot... well more than that... and even more...

Licence?
--------
Copyleft (ɔ) - Tontof - http://tontof.net

Use KrISS MVVM at your own risk.

[Free software means users have the four essential freedoms](http://www.gnu.org/philosophy/philosophy.html):
* to run the program
* to study and change the program in source code form
* to redistribute exact copies, and
* to distribute modified versions.