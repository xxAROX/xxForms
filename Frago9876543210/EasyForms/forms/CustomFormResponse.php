<?php
declare(strict_types=1);
namespace Frago9876543210\EasyForms\forms;
use Frago9876543210\EasyForms\elements\{Dropdown, Element, Input, Label, Slider, StepSlider, Toggle};
use pocketmine\form\FormValidationException;

use function array_shift;
use function get_class;


class CustomFormResponse{
	/** @var Element[] */
	private array $elements;

	/**
	 * @param Element[] $elements
	 */
	public function __construct(array $elements){
		$this->elements = $elements;
	}

	/**
	 * @return Dropdown
	 */
	public function getDropdown(): Dropdown{
		return $this->tryGet(Dropdown::class);
	}

	/**
	 * @param string $expected
	 *
	 * @return Element|mixed
	 * @internal
	 *
	 */
	public function tryGet(string $expected = Element::class){ //why PHP still hasn't templates???
		if (($element = array_shift($this->elements)) instanceof Label) {
			return $this->tryGet($expected); //remove useless element
		} else if ($element === null || !($element instanceof $expected)) {
			throw new FormValidationException("Expected a element with of type $expected, got " . get_class($element));
		}
		return $element;
	}

	/**
	 * @return Input
	 */
	public function getInput(): Input{
		return $this->tryGet(Input::class);
	}

	/**
	 * @return Slider
	 */
	public function getSlider(): Slider{
		return $this->tryGet(Slider::class);
	}

	/**
	 * @return StepSlider
	 */
	public function getStepSlider(): StepSlider{
		return $this->tryGet(StepSlider::class);
	}

	/**
	 * @return Toggle
	 */
	public function getToggle(): Toggle{
		return $this->tryGet(Toggle::class);
	}

	/**
	 * @return Element[]
	 */
	public function getElements(): array{
		return $this->elements;
	}

	/**
	 * @return mixed[]
	 */
	public function getValues(): array{
		$values = [];
		foreach ($this->elements as $element) {
			if ($element instanceof Label) {
				continue;
			}
			$values[] = $element instanceof Dropdown ? $element->getSelectedOption() : $element->getValue();
		}
		return $values;
	}
}