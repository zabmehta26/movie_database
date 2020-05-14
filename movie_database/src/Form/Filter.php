<?php
namespace Drupal\movie_database\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class Filter extends FormBase {

  /**
     * This function is used to return formid.
     * @return formid
  */
  public function getFormId() {
    return 'resume_form';
  }
  /**
     * This function is used to return $form object.
     * @return mixed
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }
  /**
     * This function is used to redirect after submission to movielist page.
     * @param  $form array ,$form_state Default parameters for form submission.
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $input = $form_state->getUserInput();
    $key = $input['search'];
    $url = Url::fromRoute('movie_database_movie', [], ['query' => ['word' => $key]]);
    $form_state->setRedirectUrl($url);
  }
}
?>
