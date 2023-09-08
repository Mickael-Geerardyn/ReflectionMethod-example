<?php
/**
 * @param  Model  $model
 * @param  string  $fieldIdentifier
 * @return void
 */
protected function unsetField(Model $model, string $fieldIdentifier)
{
	$class = get_class($model);
	$method = 'set'.$fieldIdentifier;

	if (method_exists($class, $method)) {
		// We use reflection to check what parameter type the setter expects.
		// If it expects an int we can't unset it they're typically ids
		// and it wouldn't make sense to set them to zero
		$reflectedMethod = new \ReflectionMethod($class, $method);
		$paramType = $reflectedMethod->getParameters()[0]->getType();
		if ((string)$paramType === 'int') {
			\Log::error("cannot unset field. Setter $method only accepts integers on model $class");
		} else {
			$model->{$method}('');
			$model->save();
		}
	} else {
		\Log::warning("could not find setter $method on model $class");
	}
}