<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "ai_analytics";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">

            <!-- Overview Statistics -->
            <div class="card mb15">
                <div class="card-header">
                    <h4><?php echo app_lang('ai_usage_overview'); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-primary">
                                    <span data-feather="message-square" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['total_queries'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('total_ai_queries'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-info">
                                    <span data-feather="activity" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['queries_today'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('queries_today'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-success">
                                    <span data-feather="users" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['active_users'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('active_ai_users'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-warning">
                                    <span data-feather="zap" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['total_tokens'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('total_tokens_used'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Statistics -->
            <div class="card mb15">
                <div class="card-header">
                    <h4><?php echo app_lang('subscription_statistics'); ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-primary">
                                    <span data-feather="credit-card" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['total_subscriptions'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('total_subscribers'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-success">
                                    <span data-feather="check-circle" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['active_subscriptions'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('active_subscriptions'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-danger">
                                    <span data-feather="x-circle" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['canceled_subscriptions'] ?? 0); ?></h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('canceled_subscriptions'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-icon-box bg-white border">
                                <div class="widget-icon bg-info">
                                    <span data-feather="trending-up" class="icon"></span>
                                </div>
                                <div class="widget-details">
                                    <h1><?php echo number_format($stats['conversion_rate'] ?? 0, 1); ?>%</h1>
                                    <span class="bg-transparent-white"><?php echo app_lang('conversion_rate'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Usage Chart -->
                <div class="col-md-8">
                    <div class="card mb15">
                        <div class="card-header">
                            <h4><?php echo app_lang('ai_usage_chart'); ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="usage-chart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Users -->
                <div class="col-md-4">
                    <div class="card mb15">
                        <div class="card-header">
                            <h4><?php echo app_lang('top_ai_users'); ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($top_users)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th><?php echo app_lang('user'); ?></th>
                                                <th class="text-end"><?php echo app_lang('queries'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_users as $user): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar avatar-xs mr10">
                                                                <img src="<?php echo get_avatar($user->image); ?>" alt="">
                                                            </span>
                                                            <?php echo $user->first_name . ' ' . $user->last_name; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-end"><?php echo number_format($user->query_count); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center"><?php echo app_lang('no_data_available'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Conversations -->
            <div class="card mb15">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4><?php echo app_lang('recent_ai_conversations'); ?></h4>
                    <a href="<?php echo get_uri('settings/export_ai_logs'); ?>" class="btn btn-sm btn-secondary">
                        <span data-feather="download" class="icon-16"></span> <?php echo app_lang('export_logs'); ?>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_conversations)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('user'); ?></th>
                                        <th><?php echo app_lang('query'); ?></th>
                                        <th><?php echo app_lang('tokens'); ?></th>
                                        <th><?php echo app_lang('date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_conversations as $conv): ?>
                                        <tr>
                                            <td><?php echo $conv->user_name ?? 'Unknown'; ?></td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;" title="<?php echo htmlspecialchars($conv->user_query); ?>">
                                                    <?php echo htmlspecialchars(substr($conv->user_query, 0, 100)) . (strlen($conv->user_query) > 100 ? '...' : ''); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($conv->tokens_used ?? 0); ?></td>
                                            <td><?php echo format_to_relative_time($conv->created_at); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center"><?php echo app_lang('no_conversations_yet'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Active Subscribers List -->
            <div class="card mb15">
                <div class="card-header">
                    <h4><?php echo app_lang('active_subscribers'); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($subscribers)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo app_lang('user'); ?></th>
                                        <th><?php echo app_lang('status'); ?></th>
                                        <th><?php echo app_lang('plan'); ?></th>
                                        <th><?php echo app_lang('period_end'); ?></th>
                                        <th><?php echo app_lang('queries_this_month'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscribers as $sub): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-xs mr10">
                                                        <img src="<?php echo get_avatar($sub->image ?? ''); ?>" alt="">
                                                    </span>
                                                    <?php echo ($sub->first_name ?? '') . ' ' . ($sub->last_name ?? ''); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = 'secondary';
                                                if ($sub->status === 'active') $status_class = 'success';
                                                elseif ($sub->status === 'canceled') $status_class = 'danger';
                                                elseif ($sub->status === 'past_due') $status_class = 'warning';
                                                ?>
                                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo ucfirst($sub->status); ?></span>
                                            </td>
                                            <td><?php echo $sub->plan_name ?? 'Standard'; ?></td>
                                            <td><?php echo $sub->current_period_end ? format_to_date($sub->current_period_end) : '-'; ?></td>
                                            <td><?php echo number_format($sub->monthly_queries ?? 0); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center"><?php echo app_lang('no_subscribers_yet'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/chart.min.js'); ?>"></script>
<script type="text/javascript">
$(document).ready(function() {
    // Usage Chart
    var ctx = document.getElementById('usage-chart').getContext('2d');
    var usageData = <?php echo json_encode($usage_chart_data ?? array('labels' => [], 'queries' => [], 'tokens' => [])); ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: usageData.labels,
            datasets: [{
                label: '<?php echo app_lang('queries'); ?>',
                data: usageData.queries,
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.3,
                fill: true,
                yAxisID: 'y'
            }, {
                label: '<?php echo app_lang('tokens'); ?> (K)',
                data: usageData.tokens.map(t => t / 1000),
                borderColor: 'rgb(118, 75, 162)',
                backgroundColor: 'rgba(118, 75, 162, 0.1)',
                tension: 0.3,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: '<?php echo app_lang('queries'); ?>'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: '<?php echo app_lang('tokens'); ?> (K)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
