<?php
declare(strict_types=1);
namespace Frago9876543210\EasyForms\forms;
use Closure;
use Frago9876543210\EasyForms\elements\Button;
use Frago9876543210\EasyForms\elements\FunctionalButton;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;


class MenuForm extends Form{
	/** @var Button[] */
	protected array $buttons = [];
	protected string $text;
	private ?Closure $onSubmit = null;
	private ?Closure $onClose = null;

	/**
	 * @param string $title
	 * @param string $text
	 * @param Button[]|string[] $buttons
	 * @param Closure|null $onSubmit
	 * @param Closure|null $onClose
	 */
	public function __construct(string $title, string $text = "", array $buttons = [], ?Closure $onSubmit = null, ?Closure $onClose = null){
		parent::__construct($title);
		$this->text = $text;
		foreach ($buttons as $k => $button) {
			if (is_null($button)) {
				unset($this->buttons[$k]);
			} else if (is_string($button)) {
				$this->buttons[] = new Button($button);
			} else if ($button instanceof Button) {
				$this->buttons[] = $button;
			}
		}
		$this->setOnSubmit($onSubmit);
		$this->setOnClose($onClose);
	}

	/**
	 * @param Closure|null $onSubmit
	 *
	 * @return self
	 */
	public function setOnSubmit(?Closure $onSubmit): self{
		if ($onSubmit !== null) {
			$this->onSubmit = $onSubmit;
		}
		return $this;
	}

	/**
	 * @param Closure|null $onClose
	 *
	 * @return self
	 */
	public function setOnClose(?Closure $onClose): self{
		if ($onClose !== null) {
			$this->onClose = $onClose;
		}
		return $this;
	}

	/**
	 * @param string $text
	 *
	 * @return self
	 */
	public function setText(string $text): self{
		$this->text = $text;
		return $this;
	}

	/**
	 * @return string
	 */
	final public function getType(): string{
		return self::TYPE_MENU;
	}

	final public function handleResponse(Player $player, $data): void{
		if ($data === null) {
			if ($this->onClose !== null) {
				($this->onClose)($player, $data);
			}
		} else if (is_int($data)) {
			if (!isset($this->buttons[$data])) {
				throw new FormValidationException("Button with index $data does not exist");
			}
			$button = $this->buttons[$data];
			if ($this->onSubmit !== null) {
				$button->setValue($data);
				($this->onSubmit)($player, $button);
			} else {
				if ($button instanceof FunctionalButton) {
					$button->onClick($player);
				}
			}
		} else {
			throw new FormValidationException("Expected int or null, got " . gettype($data));
		}
	}

	/**
	 * @return array
	 */
	protected function serializeFormData(): array{
		return [
			"buttons" => array_values($this->buttons),
			"content" => $this->text,
		];
	}
}