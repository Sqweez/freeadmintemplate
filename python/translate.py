from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import sys
import time

def function(text):
    chrome_options = Options()
    chrome_options.add_experimental_option("excludeSwitches", ["enable-logging"])
    chrome_options.add_argument('--headless')
    chrome_options.add_argument('--no-sandbox')
    chrome_options.add_argument("--disable-dev-shm-usage")

    driver = webdriver.Chrome(options=chrome_options, executable_path="/usr/local/bin/chromedriver")
    #driver = webdriver.Chrome(options=chrome_options)

    url = "https://translate.yandex.ru/?source_lang=ru&target_lang=en"
    driver.get(url)

    wait = WebDriverWait(driver, 10)

    #try:
    #wait.until(EC.presence_of_element_located((By.ID, 'fakeArea')))
    textarea = driver.find_element(By.XPATH, '//*[@id="fakeArea"]')
    textarea.send_keys(text)
    #element = driver.find_element(By.XPATH, '//*[@id="textbox2"]/div[1]/div[1]')
    #element.location_once_scrolled_into_view
    #WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, '//*[@id="translation"]')))
    spinner = '//*[@id="textbox2"]/div[1]/div[2]/span'
    WebDriverWait(driver, 10).until(EC.invisibility_of_element_located((By.XPATH, spinner)))
    #time.sleep(3)
    #review_text_element = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, '//*[@id="translation"]/span/span[1]')))
    #review_text = review_text_element.text
    review_text_elements = driver.find_elements(By.CLASS_NAME, 'translation-word')
    output_text = ""
    for review_text_element in review_text_elements:
        output_text += review_text_element.get_text()
    print(output_text)
    driver.quit()

    #except Exception as e:
    #    print(str(e))
    #    driver.quit()


if __name__ == "__main__":
    input_text = sys.argv[1]
    function(input_text)
