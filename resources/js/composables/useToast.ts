import { toast } from 'vue-sonner'

/**
 * Reusable toast composable.
 *
 * @return object
 */
export function useToast() {
  /**
   * Show a success toast.
   *
   * @param  string  message the toast message
   * @param  object  options additional toast options
   * @return void
   */
  const success = (message: string, options = {}) => {
    toast.success(message, options)
  }

  /**
   * Show an error toast.
   *
   * @param  string  message the toast message
   * @param  object  options additional toast options
   * @return void
   */
  const error = (message: string, options = {}) => {
    toast.error(message, options)
  }

  /**
   * Show an info toast.
   *
   * @param  string  message the toast message
   * @param  object  options additional toast options
   * @return void
   */
  const info = (message: string, options = {}) => {
    toast.info(message, options)
  }

  /**
   * Show a warning toast.
   *
   * @param  string  message the toast message
   * @param  object  options additional toast options
   * @return void
   */
  const warning = (message: string, options = {}) => {
    toast.warning(message, options)
  }

  return {
    success,
    error,
    info,
    warning,
    toast,
  }
}
