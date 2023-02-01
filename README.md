# CorrelImages
Try to build a number between 0 and 100 for matching 2 images ...

# Contexte 
    Je travaille sur un projet plus ambitieux visant à comparer deux vidéos d'un même film, éventuellement avec des tailles différentes, des framerates différents et des longueurs différentes pour en extraire les différences (portions rajoutées ou supprimées entre les deux versions). Dans cet objectif, je suis amené - après avoir "nettoyé" les éventuelles bandes noires", à comparer 2 images. 
    Mes premières tentatives ont consisté à "mesurer" l'écart en nuances de gris entre deux images; pour deux versions "de mêmes dimensions d'images", cela est assez concluant. Malheureusement, pour deux images de tailles différentes - souffrant parfois d'un "resizing" et d'une éventuelle translation, les résultats sont nettement moins concluants. 
    Je me suis donc orienté vers la recherche de points remarquables entre deux images pour mettre en évidence une fonction "un peu homothétique" entre ces deux images me permettant de définir deux blocs - chacun dans une image - à partir desquels la "différence de gris" est efficace, concluante.
    Il est bien évident que les notions de points remarquables existent déjà (reconnaissance de formes, de visages, création d'images panoraùiques, SLAM, ...) mais je recherchais quelques techniques (technologies) "efficaces". CorrelImages permet non seulement de mettre en place ce Coefficient de Corrélation entre deux images mais permet surtout de "jouer" avec les différents paramètres d'entrée et de sauvegarder les tests réalisés afin de "peaufiner" l'incidence des paramètres tant au niveau de la qualité du résultat que de son efficacité en matière de temps.
    Afin d'effectuer une majorité des traitements, je me suis aidé d'une bibliothèque déjà existante concernant le traitement d'images : JSFEAT ( https://github.com/inspirit/jsfeat ) mais j'ai du porter en javascript un algorithme visant à diminuer le nombre de points remarquables d'une image tout en respectant une certaine distribution spatiale homogène ( https://github.com/BAILOOL/ANMS-Codes ) et adapter à ma sauce l'algorithme de Ransac ( https://gist.github.com/nandor/7e74368a449924483173 ) permettant une bonne détermination de la transformation (qualifiée d'un peu homothétique plus haut) permettant de passer d'une image à l'autre.

# Algorithme d'ensemble :

a) passage d'une image couleur en image à nuance de gris

b) égalisation éventuelle de l'histogramme (normalisation d'image)

c) détermination des points remarquables par l'algorithme "Fast Corners" (Jsfeat)

d) diminution des points remarquables avec homogéinisation dans l'espace (ANMS)

e) créations de descripteurs ORB pour les points résultants permettant (Jsfeat) :

f) de "matcher" les deux images ( Jsfeat )

g) et utilisation de Ransac pour filtrer parmi mes points récupérés à l'étape précédente ceux résultant "au mieux" d'une transformation reliant les 2 iùages

finalement :

h) détermination de 2 blocs "communs" et

i) calcul de la distance entre ces deux  blocks.
