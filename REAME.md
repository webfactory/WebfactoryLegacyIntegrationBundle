Bemerkungen zur aktuellen Implementierung
======

Wir haben in FB 10908 zwei Wege überlegt, wie die Integration funktionieren könnte (vgl. dort).

Den "Retrofitting"-Ansatz halten wir im Moment für komplett gescheitert. Dabei
wird zunächst die Symfony-Anwendung ausgeführt und ihre Response so zur Verfügung gestellt,
dass die Altanwendung sie ausgeben kann.

Das Problem dabei ist, dass viele Initialisierungen der Altanwendung, die durch die
Engine stattfinden, noch nicht passiert sind und deshalb Alt-Code (aus dem sf2-Controller
heraus) evtl. nicht nutzbar ist.

Der Ansatz, zunächst die Altanwendung auszuführen - und zwar möglichst früh
im kernel.controller-Event, umgeht dieses Problem.

Einbau von neuen Features auf sf2-Basis in Altdesigns
-----

Dafür müssen wir zunächst mit der Altanwendung eine leere Seite nur mit dem "Rahmendesign" erzeugen, was evtl.
eine spezielle Action o. ä. erfordert.

Im sf2-Teil haben wir dann bisher spezielle Basis-Templates geschrieben, die mit
der vorhandenen xpath()-Funktion praktisch das gesamte Rahmendesign übernommen
haben.

Das ist etwas umständlich, weil für einen Block "html body foo bar" die Struktur

<html>
    ... xpath: alles "vor" body
    <body ...xpath:alle-body-attrs..>
       ... xpath: alles in body "vor" foo
       <foo>
          ... xpath: alles in foo "vor" bar
          <bar>
             {{ neue Ausgabe }}}
          </bar>
          ... xpath: alles in foo "nach" bar
       </foo>
       ... usw

... gemacht werden muss.

Wir können uns gut vorstellen, dass da eine weitere Twig-Funktion helfen könnte, die einen
Block/eine Ausgabe "in" das Altdesign an die Stelle eines definierten XPath-Ausdrucks
schreiben könnte und dann die alte Seite behält.

Integration in Master- oder Subrequests?
----

Der aktuelle EventListener führt die Altanwendung nur für den Master-Request aus.
Für den sf2-Code dürfte es keinen Unterschied machen, so dass auch Subrequests
möglich wären. Ggf. wäre der "LegacyApplication"-Service in den Request-Scope
zu verschieben oder besser gar kein Service mehr (weil stateful).

Die Probleme sehen wir eher Altanwendungs-seitig:
- Subrequests müssen keine eigene URL haben, die in der Altanwendung zur Basis gemacht
  werden könnte. (Es kann auch direkt ein Controller sein, oder? https://github.com/symfony/symfony/pull/6459?)
- Altcode schaut evtl. in die $_REQUEST-Werte und ist damit nicht "subrequest aware"



