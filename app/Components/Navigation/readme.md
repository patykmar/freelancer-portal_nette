﻿#Navigation
Control pro Nette Framework usnadňující tvorbu menu a drobečkové navigace

Autor: Jan Marek  
Licence: MIT

##Použití

Továrnička v presenteru:
```php
protected function createComponentNavigation($name) {
	$nav = new Navigation($this, $name);
	$nav->setupHomepage("Úvod", $this->link("Homepage:"));
	$sec = $nav->add("Sekce", $this->link("Category:", array("id" => 1)));
	$article = $sec->add("Článek", $this->link("Article:", array("id" => 1)));
	$nav->setCurrentNode($article);
	// or $article->setCurrent(TRUE);
}
```

Menu v šabloně:
```latte
{widget navigation}
```

Drobečková navigace v šabloně:
```latte
{widget navigation:breadcrumbs}
```