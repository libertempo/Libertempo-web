<?php
namespace App\Libraries\Calendrier\Evenement;

use CalendR\Event\EventInterface;
use CalendR\Period\PeriodInterface;

/**
 * Événement commun
 *
 * Ne doit contacter personne
 * Ne doit être contacté que par \App\Libraries\Collection\Ferie
 */
class Commun implements IIdentifiable, EventInterface
{
    /**
     * @var \DateTime Date de début
     */
    protected $debut;

    /**
     * @var \DateTime Date de fin
     */
    protected $fin;

    /**
     * @var string Identifiant unique au sein du calendrier
     */
    protected $uid;

    /**
     * @var string Classe html
     */
    protected $class;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string Title Html
     */
    protected $title;

    /**
     * @param string $uid Identifiant unique au sein du calendrier
     * @param \DateTime $debut Date de début
     * @param \DateTime $fin Date de fin
     * @param string $name
     * @param string $title Title Html
     * @param string $class Classe html
     */
    public function __construct($uid, \DateTime $debut, \DateTime $fin, $name, $title, $class)
    {
        $this->uid = (string) $uid;
        $this->debut = clone $debut;
        $this->fin = clone $fin;
        $this->name = (string) $name;
        $this->title = (string) $title;
        $this->class = (string) $class;
    }

    /**
     * {@inheritDoc}
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * {@inheritDoc}
     */
    public function getBegin()
    {
        return $this->debut;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnd()
    {
        return $this->fin;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return 'event ' . $this->class;
    }

    /**
     * {@inheritDoc}
     *
     * Parce que c'est n'importe quoi d'exclure la fin
     */
    public function contains(\DateTime $datetime)
    {
        return $datetime >= $this->getBegin() && $datetime <= $this->getEnd();
    }

    /**
     * {@inheritDoc}
     */
    public function containsPeriod(PeriodInterface $period)
    {
        return $this->contains($period->getBegin()) && $this->contains($period->getEnd());
    }

    /**
     * {@inheritDoc}
     *
     * Parce que c'est n'importe quoi d'exclure la fin
     */
    public function isDuring(PeriodInterface $period)
    {
        return $this->getBegin() >= $period->getBegin() && $this->getEnd() <= $period->getEnd();
    }
}
