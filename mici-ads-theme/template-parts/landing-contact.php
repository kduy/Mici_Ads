<?php
/**
 * Template Part: Landing Contact
 *
 * Contact section. If wpforms_id ACF field is set, renders WPForms shortcode.
 * Otherwise falls back to the original HTML form with Cloudflare Turnstile.
 *
 * ACF fields: contact_title, contact_subtitle, wpforms_id
 *
 * @package MiciAds
 */

$contact_title    = function_exists( 'get_field' ) ? get_field( 'contact_title' ) : '';
$contact_subtitle = function_exists( 'get_field' ) ? get_field( 'contact_subtitle' ) : '';
$wpforms_id       = function_exists( 'get_field' ) ? get_field( 'wpforms_id' ) : '';

$contact_title    = $contact_title ?: 'Liên Hệ';
$contact_subtitle = $contact_subtitle ?: 'Cho chúng tôi biết bạn cần dịch vụ nào — thiết kế, website, chụp ảnh hay marketing';
?>
<section id="contact" class="contact">
  <div class="contact__container">
    <h2 class="contact__title"><?php echo esc_html( $contact_title ); ?></h2>
    <p class="contact__subtitle"><?php echo esc_html( $contact_subtitle ); ?></p>

    <?php if ( $wpforms_id ) : ?>
      <?php echo do_shortcode( '[wpforms id="' . absint( $wpforms_id ) . '"]' ); ?>
    <?php else : ?>
      <!-- Fallback HTML form with Cloudflare Turnstile -->
      <form class="contact__form" id="contactForm">
        <div class="contact__form-row">
          <div class="form-group">
            <label for="name" class="form-group__label"><?php esc_html_e( 'Họ tên', 'mici-ads' ); ?></label>
            <input type="text" id="name" class="form-group__input"
                   placeholder="<?php esc_attr_e( 'Tên của bạn', 'mici-ads' ); ?>" required>
          </div>
          <div class="form-group">
            <label for="email" class="form-group__label"><?php esc_html_e( 'Email', 'mici-ads' ); ?></label>
            <input type="email" id="email" class="form-group__input"
                   placeholder="<?php esc_attr_e( 'email@cuaban.com', 'mici-ads' ); ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="phone" class="form-group__label"><?php esc_html_e( 'Số điện thoại / WhatsApp', 'mici-ads' ); ?></label>
          <input type="tel" id="phone" class="form-group__input"
                 placeholder="<?php esc_attr_e( '+49 xxx xxx xxxx', 'mici-ads' ); ?>">
        </div>
        <div class="form-group">
          <label for="service" class="form-group__label"><?php esc_html_e( 'Dịch vụ quan tâm', 'mici-ads' ); ?></label>
          <select id="service" class="form-group__input">
            <option value=""><?php esc_html_e( 'Chọn dịch vụ bạn cần', 'mici-ads' ); ?></option>
            <option value="design-print"><?php esc_html_e( 'Thiết kế & In ấn', 'mici-ads' ); ?></option>
            <option value="website"><?php esc_html_e( 'Website', 'mici-ads' ); ?></option>
            <option value="photo-video"><?php esc_html_e( 'Chụp ảnh & Video', 'mici-ads' ); ?></option>
            <option value="marketing"><?php esc_html_e( 'Online Marketing', 'mici-ads' ); ?></option>
            <option value="combo"><?php esc_html_e( 'Trọn gói nhiều dịch vụ', 'mici-ads' ); ?></option>
          </select>
        </div>
        <div class="form-group">
          <label for="message" class="form-group__label"><?php esc_html_e( 'Chi tiết dự án', 'mici-ads' ); ?></label>
          <textarea id="message" class="form-group__input form-group__textarea"
                    placeholder="<?php esc_attr_e( 'Mô tả nhu cầu thiết kế của bạn...', 'mici-ads' ); ?>"
                    rows="4"></textarea>
        </div>
        <!-- Cloudflare Turnstile CAPTCHA — replace data-sitekey with key from dash.cloudflare.com -->
        <div class="cf-turnstile" data-sitekey="1x00000000000000000000AA" data-theme="light"></div>
        <button type="submit" class="l-btn l-btn--primary l-btn--full">
          <?php esc_html_e( 'Gửi tin nhắn', 'mici-ads' ); ?>
        </button>
      </form>
    <?php endif; ?>
  </div>
</section>
