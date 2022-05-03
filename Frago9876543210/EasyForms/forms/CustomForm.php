<?php
declare(strict_types=1);
namespace Frago9876543210\EasyForms\forms;
use Closure;
use Frago9876543210\EasyForms\elements\Element;
use pocketmine\form\FormValidationException;

use function array_merge;
use function gettype;
use function is_array;


class CustomForm extends Form{
	/** @var Element[] */
	protected array $elements;
	/** @var Closure */
	private Closure $onSubmit;
	/** @var Closure|null */
	private ?Closure $onClose;

	/**
	 * @param string $title
	 * @param Element[] $elements
	 * @param Closure $onSubmit
	 * @param Closure|null $onClose
	 */
	public function __construct(string $title, array $elements, Closure $onSubmit, ?Closure $onClose = null){
		parent::__construct($title);
		$this->elements = $elements;
		$this->onSubmit = $onSubmit;
		$this->onClose = $onClose;
		$this->onSubmit = $onSubmit;
	}

	/**
	 * @param Element ...$elements
	 *
	 * @return $this
	 */
	public function append(Element ...$elements): self{
		$this->elements = array_merge($this->elements, $elements);
		return $this;
	}

	/**
	 * @return string
	 */
	final public function getType(): string{
		return self::TYPE_CUSTOM_FORM;
	}

	final public function handleResponse(Player $player, $data): void{
		if ($data === null) {
			if ($this->onClose !== null) {
				($this->onClose)($player);
			}
		} else if (is_array($data)) {
			foreach ($data as $index => $value) {
				if (!isset($this->elements[$index])) {
					throw new FormValidationException("Element at index $index does not exist");
				}
				$element = $this->elements[$index];
				$element->validate($value);
				$element->setValue($value);
			}
			($this->onSubmit)($player, new CustomFormResponse($this->elements));
		} else {
			throw new FormValidationException("Expected array or null, got " . gettype($data));
		}
	}

	/**
	 * @return array
	 */
	protected function serializeFormData(): array{
		return ["content" => $this->elements];
	}
}