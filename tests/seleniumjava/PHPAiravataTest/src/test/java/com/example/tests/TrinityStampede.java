package com.example.tests;

import java.util.regex.Pattern;
import java.util.concurrent.TimeUnit;

import com.example.tests.utils.FileReadUtils;
import org.junit.*;
import static org.junit.Assert.*;
import static org.hamcrest.CoreMatchers.*;
import org.openqa.selenium.*;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.support.ui.Select;

/*
 **********Executing Trinity Application on Stampede**********
 * Created by Airavata on 9/16/14.
 * The script generates Trinity application execution on Stampede
 * Enter your experiment-name and experiment-description in the script
*/

public class TrinityStampede extends UserLogin {
  private WebDriver driver;
  private String baseUrl;
  private boolean acceptNextAlert = true;
  private StringBuffer verificationErrors = new StringBuffer();
  private String path = null;
  private String expName = null;

  @Before
  public void setUp() throws Exception {
    driver = new FirefoxDriver();
    baseUrl = "http://test-drive.airavata.org/";
    path = FileReadUtils.readProperty("local.path");
    expName = FileReadUtils.readProperty("experiment.name");
    driver.manage().timeouts().implicitlyWait(30, TimeUnit.SECONDS);
  }

  @Test
  public void testTrinityStampede() throws Exception {
    driver.get(baseUrl + "/PHP-Reference-Gateway/index.php");
      authenticate(driver);
    driver.findElement(By.linkText("Experiment")).click();
    driver.findElement(By.id("create-experiment")).click();
    driver.findElement(By.id("experiment-name")).clear();
    driver.findElement(By.id("experiment-name")).sendKeys(expName + "Trinity-Stampede");
      waitTime (500);
    driver.findElement(By.id("experiment-description")).clear();
    driver.findElement(By.id("experiment-description")).sendKeys("Test Experiment");
      waitTime(500);
    new Select(driver.findElement(By.id("project"))).selectByVisibleText(FileReadUtils.readProperty("project.name"));
      waitTime(500);
    new Select(driver.findElement(By.id("application"))).selectByVisibleText("Trinity");
      waitTime(500);
    driver.findElement(By.name("continue")).click();
      waitTime(500);
    driver.findElement(By.id("RNA_Seq_Left_Input")).sendKeys(path + "/Stampede/Trinity/reads.left.fq");
      waitTime(500);
    driver.findElement(By.id("RNA_Seq_Right_Input")).sendKeys(path + "/Stampede/Trinity/reads.right.fq");
      waitTime(500);
    driver.findElement(By.id("node-count")).clear();
    driver.findElement(By.id("node-count")).sendKeys("2");
    driver.findElement(By.id("cpu-count")).clear();
    driver.findElement(By.id("cpu-count")).sendKeys("5");
    driver.findElement(By.id("wall-time")).clear();
    driver.findElement(By.id("wall-time")).sendKeys("31");
    driver.findElement(By.name("save")).click();
      waitTime(500);
    driver.findElement(By.name("launch")).click();
      waitTime(500);
  }

    private void waitTime(int i) {
        try {
            Thread.sleep(i);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }

  @After
  public void tearDown() throws Exception {
    driver.quit();
    String verificationErrorString = verificationErrors.toString();
    if (!"".equals(verificationErrorString)) {
      fail(verificationErrorString);
    }
  }

  private boolean isElementPresent(By by) {
    try {
      driver.findElement(by);
      return true;
    } catch (NoSuchElementException e) {
      return false;
    }
  }

  private boolean isAlertPresent() {
    try {
      driver.switchTo().alert();
      return true;
    } catch (NoAlertPresentException e) {
      return false;
    }
  }

  private String closeAlertAndGetItsText() {
    try {
      Alert alert = driver.switchTo().alert();
      String alertText = alert.getText();
      if (acceptNextAlert) {
        alert.accept();
      } else {
        alert.dismiss();
      }
      return alertText;
    } finally {
      acceptNextAlert = true;
    }
  }
}