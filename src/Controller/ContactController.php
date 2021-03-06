<?php

namespace App\Controller;

use Cake\Core\Configure;
use Tools\Form\ContactForm;
use Tools\Mailer\Email;

/**
 * @property \Captcha\Controller\Component\CaptchaComponent $Captcha
 */
class ContactController extends AppController {

	/**
	 * @var string|false
	 */
	public $modelClass = false;

	/**
	 * @var array
	 */
	public $components = [
		'Captcha.Captcha',
	];

	/**
	 * @var array
	 */
	public $helpers = [
		'Tools.Obfuscate', 'Captcha.Captcha',
	];

	/**
	 * @return void
	 */
	public function initialize() {
		parent::initialize();

		if (Configure::read('debug')) {
			return;
		}
		$this->loadComponent('Csrf');
		$this->loadComponent('Security');
	}

	/**
	 * @return void
	 */
	public function index() {
		$contact = new ContactForm();

		if ($this->Common->isPosted()) {
			$name = $this->request->getData('name');
			$email = $this->request->getData('email');
			$subject = $this->request->getData('subject');
			$message = $this->request->getData('body');

			if (Configure::read('debug')) {
				$this->Flash->info('In debug mode there is no captcha validation necessary.');
			} else {
				$this->Captcha->addValidation($contact->getValidator());
			}

			if ($contact->execute($this->request->getData())) {
				$this->_send($name, $email, $subject, $message);
			} else {
				$this->Flash->error(__('formContainsErrors'));
			}
		} else {
			// prepopulate form
			$this->request->data = $this->request->getQuery();

			# try to autofill fields
			$user = (array)$this->request->session()->read('Auth.User');
			if (!empty($user['email'])) {
				$this->request->data['email'] = $user['email'];
			}
			if (!empty($user['username'])) {
				$this->request->data['name'] = $user['username'];
			}
		}

		$this->set(compact('contact'));
	}

	/**
	 * @param string $fromName
	 * @param string $fromEmail
	 * @param string $subject
	 * @param string $message
	 *
	 * @return \Cake\Http\Response|null
	 */
	protected function _send($fromName, $fromEmail, $subject, $message) {
		$adminEmail = Configure::read('Config.adminEmail');
		$adminName = Configure::read('Config.adminName');

		// Send email to Admin
		Configure::write('Email.live', true);
		$email = new Email();
		$email->to($adminEmail, $adminName);

		$email->subject(Configure::read('Config.pageName') . ' - ' . __('contact via form'));
		$email->template('contact');
		$email->viewVars(compact('message', 'subject', 'fromEmail', 'fromName'));
		if ($email->send()) {
			$this->Flash->success(__('contactSuccessfullySent {0}', $fromEmail));
			return $this->redirect(['action' => 'index']);
		}
		if (Configure::read('debug')) {
			$this->Flash->warning($email->getError());
		}
		$this->log($email->getError());
		$this->Flash->error(__('Contact Email could not be sent'));
	}

}
