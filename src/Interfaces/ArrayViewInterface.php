<?php

declare(strict_types=1);

namespace Smoren\ArrayView\Interfaces;

/**
 * Interface for a view of an array with additional methods
 * for filtering, mapping, and transforming the data.
 *
 * @template T The type of elements in the array
 *
 * @extends \ArrayAccess<int, T|array<T>>
 * @extends \IteratorAggregate<int, T>
 */
interface ArrayViewInterface extends \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Creates an ArrayView instance from the given source array or ArrayView.
     *
     * * If the source is not an ArrayView, a new ArrayView is created with the provided source.
     * * If the source is an ArrayView and the `readonly` parameter is specified as `true`,
     * a new readonly ArrayView is created.
     * * If the source is an ArrayView and it is already readonly, the same ArrayView is returned.
     *
     * @param array<T>|ArrayViewInterface<T> $source The source array or ArrayView to create a view from.
     * @param bool|null $readonly Optional flag to indicate whether the view should be readonly.
     *
     * @return ArrayViewInterface<T> An ArrayView instance based on the source array or ArrayView.
     */
    public static function toView(&$source, ?bool $readonly = null): ArrayViewInterface;

    /**
     * Creates an unlinked from source ArrayView instance from the given source array or ArrayView.
     *
     * * If the source is not an ArrayView, a new ArrayView is created with the provided source.
     * * If the source is an ArrayView and the `readonly` parameter is specified as `true`,
     * a new readonly ArrayView is created.
     * * If the source is an ArrayView and it is already readonly, the same ArrayView is returned.
     *
     * @param array<T>|ArrayViewInterface<T> $source The source array or ArrayView to create a view from.
     * @param bool|null $readonly Optional flag to indicate whether the view should be readonly.
     *
     * @return ArrayViewInterface<T> An ArrayView instance based on the source array or ArrayView.
     */
    public static function toUnlinkedView($source, ?bool $readonly = null): ArrayViewInterface;

    /**
     * Returns the array representation of the view.
     *
     * @return array<T> The array representation of the view.
     */
    public function toArray(): array;

    /**
     * Filters the elements in the view based on a predicate function.
     *
     * @param callable(T): bool $predicate Function that returns a boolean value for each element.
     *
     * @return ArrayViewInterface<T> A new view with elements that satisfy the predicate.
     */
    public function filter(callable $predicate): ArrayViewInterface;

    /**
     * Checks if all elements in the view satisfy a given predicate function.
     *
     * @param callable(T): bool $predicate Function that returns a boolean value for each element.
     *
     * @return MaskSelectorInterface Boolean mask for selecting elements that satisfy the predicate.
     */
    public function is(callable $predicate): MaskSelectorInterface;

    /**
     * Returns a subview of this view based on a selector or string slice.
     *
     * @param ArraySelectorInterface|string $selector The selector or string to filter the subview.
     * @param bool|null $readonly Flag indicating if the subview should be read-only.
     *
     * @return ArrayViewInterface<T> A new view representing the subview of this view.
     */
    public function subview($selector, bool $readonly = null): ArrayViewInterface;

    /**
     * Applies a transformation function to each element in the view.
     *
     * @param callable(T, int): T $mapper Function to transform each element.
     *
     * @return ArrayViewInterface<T> this view.
     */
    public function apply(callable $mapper): self;

    /**
     * Applies a transformation function using another array or view as rhs values for a binary operation.
     *
     * @template U The type rhs of a binary operation.
     *
     * @param array<U>|ArrayViewInterface<U> $data The rhs values for a binary operation.
     * @param callable(T, U, int): T $mapper Function to transform each pair of elements.
     *
     * @return ArrayViewInterface<T> this view.
     */
    public function applyWith($data, callable $mapper): self;

    /**
     * Sets new values for the elements in the view.
     *
     * @param array<T>|ArrayViewInterface<T>|T $newValues The new values to set.
     *
     * @return ArrayViewInterface<T> this view.
     */
    public function set($newValues): self;

    /**
     * Return true if view is readonly, otherwise false.
     *
     * @return bool
     */
    public function isReadonly(): bool;

    /**
     * Return size of the view.
     *
     * @return int
     */
    public function count(): int;

    /**
     * @param numeric|string|ArraySelectorInterface $offset
     *
     * @return bool
     *
     * {@inheritDoc}
     */
    public function offsetExists($offset): bool;

    /**
     * @param numeric|string|ArraySelectorInterface $offset
     *
     * @return T|array<T>
     *
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset);

    /**
     * @param numeric|string|ArraySelectorInterface $offset
     * @param T|array<T>|ArrayViewInterface<T> $value
     *
     * @return void
     *
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value): void;

    /**
     * @param numeric|string|ArraySelectorInterface $offset
     *
     * @return void
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset): void;

    /**
     * Return iterator to iterate the view elements.
     *
     * @return \Generator<int, T>
     */
    public function getIterator(): \Generator;
}
