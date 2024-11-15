
# MelkoFramework PHP

**MelkoFramework PHP** — is a mini framework with a basic architecture that 
can be run on any PHP hosting (PHP >=7.4).
The framework includes simple route handling and provides an MVC structure for small applications.


## Project structure

- **`core/`** - Main framework code.
- **`databases/`** - Database SQLite folder.
- **`src/`** - Main components of the application.
- **`index.php`** - Entry point for all requests.

## Features

- Lightweight and minimalistic.
- Easy to configure.
- Works on any PHP hosting with minimal dependencies.
- Routes requests via GET parameters or path segments
  (e.g., `/controller/action`). This makes it easy to use the framework on hosting platforms without custom web server configuration, as all requests can be routed through `index.php`.

## Warning

If you are using this framework on a hosting environment without the ability to configure the web server (e.g., no URL rewrites to direct all requests to `index.php`), please be aware that **your database file could be exposed to malicious users**. If a user discovers the path and name of your database file, they may be able to download it directly.

To avoid this security risk, ensure that:
- All requests are routed through `index.php`.
- Sensitive files, such as your database, are not publicly accessible or are stored outside the web root.
- Consider using additional security measures, such as disabling directory listings and ensuring your server is properly configured to block direct access to sensitive files.

## Installation

1. Download the repository as a ZIP file (or clone it):
   ```bash
   git clone https://github.com/gotyefrid/melkoframework-php.git
   ```
2. Extract it to the desired location.
3. To create the basic users table, you can use the built-in MigrationController by calling `domen.com/migration/migrate`.
   To create a user, call `domen.com/migration/createuser`.


## Routing

The routing system allows you to route requests in two ways:

1. **Using GET Parameters**:
   Routes are defined using a GET parameter (e.g., `?route=home/index`). You can customize the name of the GET parameter by modifying the value of `\core\Request::$routeParameterName`.

   Example:
   ```text
   ?route=home/index
   ```

2. **Using URL Path (default way)**:
   You can also route requests using a path format like `/controller/action`. The controller should be located in the `src/controllers/` directory and should follow the naming convention `NameController.php`. The action is the method inside the controller and must be prefixed with `action` (e.g., `actionIndex` for the `index` action).

   Example:
   ```text
   /home/index
   ```

   This would call the `HomeController`'s `actionIndex` method.

### Default Routing

By default, the framework uses the route `home/index`. You can change this default route by modifying the `\core\Request::$defaultRoute` property.

### Example

1. Define a route using a GET parameter:
   ```text
   ?route=home/index
   ```

2. Define a route using a URL path:
   ```text
   /home/index
   ```

3. The controller file should be located in `src/controllers/HomeController.php`, and the action method should be named `actionIndex`.

You can modify the routing behavior by changing the default values of `\core\Request::$routeParameterName` and `\core\Request::$defaultRoute`.


## Models

For models that represent database tables, the class should extend `core\Model`, and the table name should follow the convention. 
Models should implement abstract methods.
Example:

```php
namespace src\models;

use core\Model;

class User extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $email = '';
    
    // Attributes that represent database columns
    public array $attributes = [
        'id',
        'name',
        'email',
        // ....
    ];
    
    public static function tableName(): string
    {
        return 'users'; // Set the table name if it differs from the class name
    }
    
    public function validate(): bool 
    {
        // implement
    }
}
```

If the model does not represent a table (e.g., for simple data handling or other logic), there is no need to extend `core\Model`. You can create any class you need for your application.

## Views

Views are responsible for rendering the content to the user. Views should be stored in the `src/views/{controller}` directory, where `{controller}` corresponds to the name of the controller.

### Controller Example:

For example, in the `UserController`, the view rendering can be done like this in the `actionIndex()` method:

```php
namespace src\controllers;

use core\Controller;

class UserController extends Controller
{
    public function actionIndex()
    {
        $users = User::getAllUsers(); // Your Fetching data

        // Rendering the view with data
        return $this->render('index', ['users' => $users]);
    }
}
```

### Views Folder Structure:

1. **Controller Views**: Each controller should have a corresponding folder in `src/views/`. Inside this folder, the views for that controller should be placed. For example, views for the `UserController` will be located in `src/views/user/`.

   Example of a view file: `src/views/user/index.php`

2. **Layouts**: The framework includes a main layout file located in `src/views/layouts/main.php`. This layout serves as the primary template for the page structure. Views for each controller can be rendered within this layout.

   The layout might contain the basic HTML structure such as `<html>`, `<head>`, and a `<body>` tag. The content from the controller views will be inserted into the layout.


## License

The project is distributed under the MIT license.
