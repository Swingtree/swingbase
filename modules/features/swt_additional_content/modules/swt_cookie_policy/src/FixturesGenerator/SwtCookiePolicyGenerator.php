<?php

namespace Drupal\swt_cookie_policy\FixturesGenerator;


use Drupal\data_fixtures\Interfaces\Generator;
use Drupal\node\Entity\Node;
use Drupal\swb_content_development\Services\SwbGenerator;

class SwtCookiePolicyGenerator implements Generator {

  /**
   * @var SwbGenerator
   */
  private $generator;

  public function __construct(SwbGenerator $generator) {
    $this->generator = $generator;
  }

  /**
   * @inheritDoc
   */
  public function load() {

    $nids = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route', 'cookie_policy.info')
      ->execute();

    if( empty($nids)) {

      Node::create(
        [
          'type' => 'additional_content',
          'title' => 'Privacy Policy',
          'field_route' => [
            'value' => 'cookie_policy.info',
          ],
          'body' => [
            'summary' => 'Please set your cookie policy. We use cookies to ensure that we give you the best experience on our website. This includes cookies from third party social media websites if you visit a page which contains embedded content from social media. Such third party cookies may track your use of this website. We and our partners also use cookies to ensure we show you advertising that is relevant to you. If you continue without changing your settings, we\'ll assume that you agree to receive all cookies from this website. However, you can change your cookie settings at any time.',
            'value' => "<h1>Cookies Policy</h1><p>Last updated: April 12, 2018</p><p>toto (\"us\", \"we\", or \"our\") uses cookies on the toto.com website (the \"Service\"). By using the Service, you consent to the use of cookies.</p><p>Our Cookies Policy explains what cookies are, how we use cookies, how third-parties we may partner with may use cookies on the Service, your choices regarding cookies and further information about cookies. This Cookies Policy  for toto is powered by <a href=\"https://termsfeed.com/\">TermsFeed</a>.</p><h2>What are cookies</h2><p>Cookies are small pieces of text sent by your web browser by a website you visit. A cookie file is stored in your web browser and allows the Service or a third-party to recognize you and make your next visit easier and the Service more useful to you.</p><p>Cookies can be \"persistent\" or \"session\" cookies. Persistent cookies remain on your personal computer or mobile device when you go offline, while session cookies are deleted as soon as you close your web browser.</p><h2>How toto uses cookies</h2><p>When you use and access the Service, we may place a number of cookies files in your web browser.</p><p>We use cookies for the following purposes:</p><ul><li><p>To enable certain functions of the Service</p><p>We use both session and persistent cookies on the Service and we use different types of cookies to run the Service:</p><p>Essential cookies. We may use essential cookies to authenticate users and prevent fraudulent use of user accounts.</p></li></ul><h2>What are your choices regarding cookies</h2><p>If you'd like to delete cookies or instruct your web browser to delete or refuse cookies, please visit the help pages of your web browser.</p><p>Please note, however, that if you delete cookies or refuse to accept them, you might not be able to use all of the features we offer, you may not be able to store your preferences, and some of our pages might not display properly.</p><ul><li><p>For the Chrome web browser, please visit this page from Google: <a href=\"https://support.google.com/accounts/answer/32050\">https://support.google.com/accounts/answer/32050</a></p></li><li><p>For the Internet Explorer web browser, please visit this page from Microsoft: <a href=\"http://support.microsoft.com/kb/278835\">http://support.microsoft.com/kb/278835</a></p></li><li><p>For the Firefox web browser, please visit this page from Mozilla: <a href=\"https://support.mozilla.org/en-US/kb/delete-cookies-remove-info-websites-stored\">https://support.mozilla.org/en-US/kb/delete-cookies-remove-info-websites-stored</a></p></li><li><p>For the Safari web browser, please visit this page from Apple: <a href=\"https://support.apple.com/kb/PH21411?locale=en_US\">https://support.apple.com/kb/PH21411?locale=en_US</a></p></li><li><p>For any other web browser, please visit your web browser's official web pages.</p></li></ul><h2>Where can you find more information about cookies</h2><p>You can learn more about cookies and the following third-party websites:</p><ul><li><p>AllAboutCookies: <a href=\"http://www.allaboutcookies.org/\">http://www.allaboutcookies.org/</a></p></li><li><p>Network Advertising Initiative: <a href=\"http://www.networkadvertising.org/\">http://www.networkadvertising.org/</a></p></li></ul>",
            'format' => 'creative'
          ],
          'status'=> Node::PUBLISHED,
        ]
      )->save();
    }
  }

  /**
   * @inheritDoc
   */
  public function unLoad() {

    $nids = \Drupal::entityQuery('node')
      ->condition('type','additional_content')
      ->condition('field_route', 'cookie_policy.info')
      ->execute();

    // Delete
    foreach ($nids as $nid) {
      Node::load($nid)->delete();
    }
  }

}