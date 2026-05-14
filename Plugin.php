<?php
/**
 * Typecho 站点维护插件（可自定义联系邮箱）
 *
 * @package MaintenanceMode
 * @author I`M ZH
 * @version 1.0.0
 * @link https://imzh.cn/
 */

class MaintenanceMode_Plugin implements Typecho_Plugin_Interface
{
    // 激活插件：注册 beforeRender 钩子
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('MaintenanceMode_Plugin', 'checkMaintenanceMode');
        return 'MaintenanceMode 插件已激活，请前往设置开启维护模式并填写联系邮箱。';
    }

    // 禁用插件
    public static function deactivate()
    {
        return 'MaintenanceMode 插件已禁用，网站恢复正常访问。';
    }

    // 检查维护模式，若开启则输出美观的维护页面
    public static function checkMaintenanceMode()
    {
        $options = Helper::options();
        $pluginConfig = $options->plugin('MaintenanceMode');

        // 未开启维护模式则放行
        if (!$pluginConfig || empty($pluginConfig->maintenanceMode) || $pluginConfig->maintenanceMode != '1') {
            return;
        }

        // 获取自定义联系邮箱（如果没有设置则使用默认值）
        $contactEmail = !empty($pluginConfig->contactEmail) ? $pluginConfig->contactEmail : 'support@example.com';

        // 输出美观的维护页面
        echo self::getMaintenanceHtml($contactEmail);
        exit;
    }

    /**
     * 生成美观的维护页面 HTML（内嵌样式，支持深色模式，动态邮箱）
     *
     * @param string $email 联系邮箱
     * @return string
     */
    private static function getMaintenanceHtml($email)
    {
        // 安全转义邮箱地址，防止 XSS
        $emailSafe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>网站维护中 | 即将归来</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #f6f9fc 0%, #eef2f5 100%);
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        .maintenance-card {
            max-width: 560px;
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 2.5rem;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.2), 0 2px 6px rgba(0, 0, 0, 0.02);
            padding: 2rem 2rem 2.2rem;
            text-align: center;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .icon-group {
            font-size: 4.8rem;
            line-height: 1.2;
            margin-bottom: 1.2rem;
            letter-spacing: 0.2rem;
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.08));
        }

        .icon-group span {
            display: inline-block;
            transition: transform 0.2s ease;
        }

        .icon-group span:hover {
            transform: scale(1.02);
        }

        .spinner-wrapper {
            margin: 1.5rem 0 1rem;
        }
        .spinner {
            width: 48px;
            height: 48px;
            margin: 0 auto;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #3b82f6;
            border-right: 4px solid #93c5fd;
            border-radius: 50%;
            animation: spin 0.9s cubic-bezier(0.4, 0.2, 0.2, 0.8) infinite;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0 0.75rem;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .message {
            font-size: 1rem;
            line-height: 1.5;
            color: #334155;
            margin: 0.8rem 0 0.5rem;
            padding: 0 0.5rem;
        }

        .sub-message {
            font-size: 0.9rem;
            color: #475569;
            margin: 0.5rem 0 1.2rem;
            background: #f1f5f9;
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 40px;
        }

        .refresh-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: #1e293b;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 60px;
            cursor: pointer;
            margin: 1.2rem 0 1rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .refresh-btn:hover {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15);
        }

        .refresh-btn:active {
            transform: translateY(1px);
        }

        .contact {
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #5b6e8c;
            border-top: 1px solid #e9edf2;
            padding-top: 1.2rem;
        }

        .contact a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .contact a:hover {
            color: #1e40af;
            text-decoration: underline;
        }

        .small-note {
            font-size: 0.75rem;
            color: #6c7a91;
            margin-top: 0.8rem;
        }

        @media (max-width: 480px) {
            .maintenance-card {
                padding: 1.5rem 1.2rem 1.8rem;
            }
            .icon-group {
                font-size: 3.6rem;
                gap: 0.3rem;
            }
            h1 {
                font-size: 1.7rem;
            }
            .refresh-btn {
                padding: 0.65rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
            }
            .maintenance-card {
                background: rgba(30, 41, 59, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 25px 40px -15px rgba(0, 0, 0, 0.5);
            }
            h1 {
                color: #f1f5f9;
            }
            .message, .sub-message {
                color: #cbd5e1;
            }
            .sub-message {
                background: #1e293b;
                color: #a5b4fc;
            }
            .refresh-btn {
                background: #334155;
                color: #f8fafc;
            }
            .refresh-btn:hover {
                background: #475569;
            }
            .contact {
                border-top-color: #334155;
                color: #9ca3af;
            }
            .contact a {
                color: #60a5fa;
            }
            .spinner {
                border-color: #334155;
                border-top-color: #60a5fa;
                border-right-color: #93c5fd;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="icon-group">
            <span>🔧</span>
            <span>⚙️</span>
            <span>🛠️</span>
        </div>
        <div class="spinner-wrapper">
            <div class="spinner" aria-label="维护进行中"></div>
        </div>
        <h1>网站维护中</h1>
        <p class="message">
            为了提供更稳定、更优质的服务，我们正在对网站进行系统升级与维护。
        </p>
        <p class="message">
            网站暂时无法访问，技术人员正在全力工作中 🚀
        </p>
        <div class="sub-message">
            ⏱️ 预计很快恢复 · 感谢您的耐心
        </div>
        <button class="refresh-btn" onclick="window.location.reload();">
            🔄 刷新页面
        </button>
        <div class="contact">
            📧 如有紧急事宜，请发送邮件至 <a href="mailto:{$emailSafe}">{$emailSafe}</a>
            <div class="small-note">
                您稍后可以点击上方按钮重新访问，或等待维护完成后自动恢复。
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    // 插件配置页面（后台）
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 维护模式开关
        $maintenanceMode = new Typecho_Widget_Helper_Form_Element_Radio(
            'maintenanceMode',
            array('0' => '关闭', '1' => '开启'),
            '0',
            _t('站点维护模式'),
            _t('开启后，所有访客将看到美观的维护页面（管理员仍可登录后台）')
        );
        $form->addInput($maintenanceMode);

        // 联系邮箱配置（可自定义）
        $contactEmail = new Typecho_Widget_Helper_Form_Element_Text(
            'contactEmail',
            null,
            'support@example.com',
            _t('联系邮箱'),
            _t('填写用于接收紧急事宜的邮箱地址，将显示在维护页面中。')
        );
        $form->addInput($contactEmail);
    }

    // 个人配置（本插件不需要）
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
}