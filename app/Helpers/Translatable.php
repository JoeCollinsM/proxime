<?php

namespace App\Helpers;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait Translatable
{
    private static $trans;

    /*
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     * */
    function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    function init()
    {
        if (self::$trans == null) {
            self::$trans = [];
        }
        if (!isset(self::$trans[$this->id])) {
            $lang = App::getLocale();
            self::$trans[$this->id] = $this->translations()->where('language', $lang)->pluck('content', 'column_name');
        }
    }

    public function translate($column, $default = null)

    {

        $this->init();
        return self::$trans[$this->id][$column] ?? $default;

    }

    public function isTranslatable($column)

    {

        if (in_array('*', $this->translatable_columns)) return true;
        return in_array($column, $this->translatable_columns);

    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        $attributes = $this->addTranslateAttributesToArray($attributes);

        return $attributes;
    }

    function addTranslateAttributesToArray($attributes)
    {
        if (!in_array('*', $this->translatable_columns)) {
            foreach ($this->translatable_columns as $column) {
                if (isset($attributes[$column])) $attributes[$column] = $this->translate($column, $attributes[$column]);
            }
        } else {
            foreach ($attributes as $column => $value) {
                $attributes[$column] = $this->translate($column, $attributes[$column]);
            }
        }
        return $attributes;
    }

    /**
     * Transform a raw model value using mutators, casts, etc.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function transformModelValue($key, $value)
    {
        // If the attribute has a get mutator, we will call that then return what
        // it returns as the value, which is useful for transforming values on
        // retrieval from the model to a form that is more useful for usage.
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        // If the attribute exists within the cast array, we will convert it to
        // an appropriate native PHP type dependent upon the associated value
        // given with the key in the pair. Dayle made this comment line up.
        if ($this->hasCast($key)) {
            return $this->castAttribute($key, $value);
        }

        if ($this->isTranslatable($key)) {
            return $this->translate($key, $value);
        }

        // If the attribute is listed as a date, we will convert it to a DateTime
        // instance on retrieval, which makes it quite convenient to work with
        // date fields without having to create a mutator for each property.
        if ($value !== null
            && \in_array($key, $this->getDates(), false)) {
            return $this->asDateTime($value);
        }

        return $value;
    }
}
