<div class="wrap">
<?php printf('<h2>%s</h2>',
			__('Logic Hop Integrations', 'logichop')
		);
?>
<p>Supercharge your content personlization by integrating with the tools you're already using&mdash;including page builders, analytics tools, marketing platforms, and more.</p>
<table class="wp-list-table widefat">
    <thead>
        <tr>
            <th scope="col" class="manage-column column-name column-primary">Integration</th>
            <th scope="col" class="manage-column column-auto-updates">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $this->logic->get_integrations() as $k => $i ): ?>
            <tr class="<?php echo ( $i['status'] == 'active' ? 'active': 'inactive' ); ?>">
                <td class="column-primary">
                    <strong><?php esc_html_e( $i['name'] ); ?></strong>
                    <p><?php esc_html_e( $i['desc'] ); ?></p>
                </td>
                <td class="column-auto-updates">
                    <?php switch( $i['status'] ) {
                        case 'active':
                            ?>
                            <span>Active</span>
                            <?php
                            break;
                        case 'installed':
                            $activate_link = wp_nonce_url(
                                self_admin_url( 'plugins.php?action=activate&plugin=' . $k),
                                'activate-plugin_' . $k
                            )
                            ?>
                            <a class="button button-primary" href="<?php echo esc_url( $activate_link ); ?>">Activate</a>
                            <?php
                            break;
                        case 'unavailable':
                            $install_key = substr( $k, 0, strpos( $k, '/' ) );
                            $install_link = wp_nonce_url(
                                self_admin_url( 'update.php?action=install-plugin&plugin=' . $install_key),
                                'install-plugin_' . $install_key
                            )
                            ?>
                            <a class="button" href="<?php echo esc_url( $install_link ); ?>">Install</a>
                            <?php
                            break;
                    } ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>